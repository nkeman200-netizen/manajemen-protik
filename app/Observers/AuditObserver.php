<?php

namespace App\Observers;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    protected function log($model, $action, $old = null, $new = null)
    {
        AuditTrail::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'old_values'     => $old,
            'new_values'     => $new,
        ]);
    }

    public function created($model)
    {
        $this->log($model, 'created', null, $model->getAttributes());
    }

    public function updated($model)
    {
        $this->log($model, 'updated', array_intersect_key($model->getOriginal(), $model->getChanges()), $model->getChanges());
    }

    public function deleted($model)
    {
        $this->log($model, 'deleted', $model->getOriginal(), null);
    }
}
