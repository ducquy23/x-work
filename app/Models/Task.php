<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Task extends Model
{
    use LogsActivity;
    protected $fillable = [
        'project_id',
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'priority',
        'progress',
        'due_date',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status', 'priority', 'progress', 'due_date', 'assignee_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * @return HasMany
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class);
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }
}

