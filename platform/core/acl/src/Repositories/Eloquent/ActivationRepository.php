<?php

namespace Botble\ACL\Repositories\Eloquent;

use Botble\ACL\Models\Activation;
use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\Base\Models\BaseModel;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class ActivationRepository extends RepositoriesAbstract implements ActivationInterface
{
    /**
     * The activation expiration time, in seconds.
     */
    protected int $expires = 259200;

    public function createUser(User $user): BaseModel|Model
    {
        /**
         * @var Model $activation
         */
        $activation = $this->model;

        $code = $this->generateActivationCode();

        $activation->fill(compact('code'));

        $activation->user_id = $user->getKey();

        $activation->save();

        $this->resetModel();

        return $activation;
    }

    public function exists(User $user, $code = null): BaseModel|bool
    {
        $expires = $this->expires();

        /**
         * @var Builder $activation
         */
        $activation = $this
            ->model
            ->newQuery()
            ->where('user_id', $user->getKey())
            ->where('completed', false)
            ->where('created_at', '>', $expires);

        if ($code) {
            $activation->where('code', $code);
        }

        $this->resetModel();

        return $activation->first() ?: false;
    }

    public function complete(User $user, $code): bool
    {
        $expires = $this->expires();

        /**
         * @var Activation $activation
         */
        $activation = $this
            ->model
            ->newQuery()
            ->where('user_id', $user->getKey())
            ->where('code', $code)
            ->where('completed', false)
            ->where('created_at', '>', $expires)
            ->first();

        if ($activation === null) {
            return false;
        }

        $activation->fill([
            'completed' => true,
            'completed_at' => Carbon::now(),
        ]);

        $activation->save();

        $this->resetModel();

        return true;
    }

    public function completed(User $user): BaseModel|bool
    {
        $activation = $this
            ->model
            ->newQuery()
            ->where('user_id', $user->getKey())
            ->where('completed', true)
            ->first();

        $this->resetModel();

        return $activation ?: false;
    }

    public function remove(User $user)
    {
        /**
         * @var Activation $activation
         */
        $activation = $this->completed($user);

        if (! $activation) {
            return false;
        }

        $this->resetModel();

        return $activation->delete();
    }

    public function removeExpired()
    {
        $expires = $this->expires();

        return $this
            ->model
            ->newQuery()
            ->where('completed', false)
            ->where('created_at', '<', $expires)
            ->delete();
    }

    protected function expires(): Carbon
    {
        return Carbon::now()->subSeconds($this->expires);
    }

    protected function generateActivationCode(): string
    {
        return Str::random(32);
    }
}
