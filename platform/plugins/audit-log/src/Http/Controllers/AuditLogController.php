<?php

namespace Botble\AuditLog\Http\Controllers;

use Botble\AuditLog\Models\AuditHistory;
use Botble\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Botble\AuditLog\Tables\AuditLogTable;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Exception;
use Illuminate\Http\Request;

class AuditLogController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function __construct(protected AuditLogInterface $auditLogRepository)
    {
    }

    public function getWidgetActivities(BaseHttpResponse $response, Request $request)
    {
        $limit = $request->integer('paginate', 10);
        $limit = $limit > 0 ? $limit : 10;

        $histories = $this->auditLogRepository
            ->advancedGet([
                'with' => ['user'],
                'order_by' => ['created_at' => 'DESC'],
                'paginate' => [
                    'per_page' => $limit,
                    'current_paged' => $request->integer('page', 1),
                ],
            ]);

        return $response
            ->setData(view('plugins/audit-log::widgets.activities', compact('histories', 'limit'))->render());
    }

    public function index(AuditLogTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/audit-log::history.name'));

        return $dataTable->renderTable();
    }

    public function destroy(AuditHistory $log, Request $request, BaseHttpResponse $response)
    {
        try {
            $this->auditLogRepository->delete($log);

            event(new DeletedContentEvent(AUDIT_LOG_MODULE_SCREEN_NAME, $request, $log));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, new AuditHistory(), AUDIT_LOG_MODULE_SCREEN_NAME);
    }

    public function deleteAll(BaseHttpResponse $response)
    {
        $this->auditLogRepository->getModel()->truncate();

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
