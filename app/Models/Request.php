<?php
// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_id',
        'applicant_id',
        'target_id',
        'target_type',
        'status',
        'responder_id',
        'response_note',
        'data'
    ];
    protected $casts = ['data' => 'array'];

    public function type()
    {
        return $this->belongsTo(RequestType::class, 'type_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    public function target()
    {
        return $this->morphTo();
    }

    public function history()
    {
        return $this->hasMany(RequestStatusHistory::class);
    }

    // Status transitions
    public function accept(User $responder, $note = null)
    {
        $this->update([
            'status' => 'accepted',
            'responder_id' => $responder->id,
            'responded_at' => now(),
            'response_note' => $note
        ]);

        $this->logStatusChange('accepted', $responder);
    }

    protected function logStatusChange($status, $actor = null)
    {
        $this->history()->create([
            'status' => $status,
            'actor_id' => $actor?->id,
            'notes' => "Status changed to {$status}"
        ]);
    }
}
