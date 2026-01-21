<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'real_estate_document_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'requires_expiry_date',
        'max_versions',
        'storage_path_template'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_expiry_date' => 'boolean',
        'max_versions' => 'integer'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public const TYPE_EWA_BILL = 'ewa_bill';
    public const TYPE_LEASE_CONTRACT = 'lease_contract';
    public const TYPE_INSPECTION_REPORT = 'inspection_report';
    public const TYPE_INSURANCE = 'insurance';
    public const TYPE_ID_DOCUMENT = 'id_document';
    public const TYPE_OTHER = 'other';
}

