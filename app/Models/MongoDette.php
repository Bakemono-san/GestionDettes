<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class MongoDette extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Collection name by date
        $this->collection = 'dettes_' . date('Y_m_d');
    }

    // Fillable attributes include nested structure
    protected $fillable = [
        'client',
        'dettes',
        'created_at',
        'updated_at'
    ];

    /**
     * Mutator to handle the client and its nested dettes and articles.
     */
    public function setClientAttribute($value)
    {
        $this->attributes['client'] = is_array($value) ? $value : json_encode($value);
    }

    public function setDettesAttribute($value)
    {
        $this->attributes['dettes'] = is_array($value) ? $value : json_encode($value);
    }
}
