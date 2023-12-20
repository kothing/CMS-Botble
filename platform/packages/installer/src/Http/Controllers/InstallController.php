<?php

namespace Botble\Installer\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Facades\BaseHelper;
use Botble\Installer\Events\EnvironmentSaved;
use Botble\Installer\Events\InstallerFinished;
use Botble\Installer\Http\Requests\SaveAccountRequest;
use Botble\Installer\Http\Requests\SaveEnvironmentRequest;
use Botble\Installer\Supports\EnvironmentManager;
use Botble\Installer\Supports\RequirementsChecker;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;

class InstallController extends Controller
{
    public function __construct(protected RequirementsChecker $requirements, protected EnvironmentManager $environmentManager)
    {
    }

    public function getWelcome()
    {
        return view('packages/installer::welcome');
    }

    public function getRequirements(Request $request)
    {
        if (! URL::hasValidSignature($request)) {
            return redirect()->route('installers.welcome');
        }

        $phpSupportInfo = $this->requirements->checkPhpVersion(get_minimum_php_version());
        $requirements = $this->requirements->check(config('packages.installer.installer.requirements'));

        return view('packages/installer::.requirements', compact('requirements', 'phpSupportInfo'));
    }

    public function getEnvironment(Request $request)
    {
        if (! URL::hasValidSignature($request)) {
            return redirect()->route('installers.welcome');
        }

        return view('packages/installer::environment');
    }

    public function postSaveEnvironment(SaveEnvironmentRequest $request)
    {
        $driverName = $request->input('database_connection');
        $connectionName = 'database.connections.' . $driverName;
        $databaseName = $request->input('database_name');

        config([
            'database.default' => $driverName,
            $connectionName => array_merge(config($connectionName), [
                'host' => $request->input('database_hostname'),
                'port' => $request->input('database_port'),
                'database' => $databaseName,
                'username' => $request->input('database_username'),
                'password' => $request->input('database_password'),
            ]),
        ]);

        try {
            DB::purge($driverName);
            DB::unprepared('USE `' . $databaseName . '`');
            DB::connection()->setDatabaseName($databaseName);
            DB::getSchemaBuilder()->dropAllTables();
            DB::unprepared(file_get_contents(base_path('database.sql')));

            File::delete(app()->bootstrapPath('cache/plugins.php'));
        } catch (QueryException $exception) {
            $errors = new MessageBag();
            $errors->add('database', $exception->getMessage());

            return back()->withInput()->withErrors($errors);
        }

        $results = $this->environmentManager->save($request);

        event(new EnvironmentSaved($request));

        BaseHelper::saveFileData(storage_path(INSTALLING_SESSION_NAME), Carbon::now()->toDateTimeString());

        return redirect()
            ->to(URL::temporarySignedRoute('installers.create_account', Carbon::now()->addMinutes(30)))
            ->with('install_message', $results);
    }

    public function getCreateAccount()
    {
        return view('packages/installer::account');
    }

    public function postSaveAccount(SaveAccountRequest $request, ActivateUserService $activateUserService)
    {
        try {
            User::truncate();

            $user = new User();
            $user->fill($request->only([
                'first_name',
                'last_name',
                'username',
                'email',
            ]));
            $user->super_user = 1;
            $user->{ACL_ROLE_MANAGE_SUPERS} = 1;
            $user->password = Hash::make($request->input('password'));
            $user->save();

            $activateUserService->activate($user);

            return redirect()
                ->to(URL::temporarySignedRoute('installers.final', Carbon::now()->addMinutes(30)));
        } catch (Exception $exception) {
            return back()->withInput()->withErrors([
                'first_name' => [$exception->getMessage()],
            ]);
        }
    }

    public function getFinish(Request $request)
    {
        if (! URL::hasValidSignature($request)) {
            return redirect()->route('installers.welcome');
        }

        event(new InstallerFinished());

        File::delete(storage_path(INSTALLING_SESSION_NAME));
        BaseHelper::saveFileData(storage_path(INSTALLED_SESSION_NAME), Carbon::now()->toDateTimeString());

        $this->environmentManager->turnOffDebugMode();

        return view('packages/installer::finished');
    }
}
