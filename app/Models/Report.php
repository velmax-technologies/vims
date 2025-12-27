<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'report_date',
        'report_data',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            $report->slug = Str::slug($report->title);
            $report->user_id = auth()->user()->id;
        });

        
    }
}
