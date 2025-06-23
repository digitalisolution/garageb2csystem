<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CarserviceProduct extends Model
{
    protected $connection = 'mysql_vehicle_details'; // Use the common database connection
    protected $table = 'vehiclevrmlist'; // Specify the table name
    public $timestamps = false;
    /**
     * Get all makes
     *
     * @return array|bool
     */
    public function getMakes()
    {
        $makes = DB::connection($this->connection) // Explicitly specify the connection
            ->table($this->table)
            ->select('Make')
            ->groupBy('Make')
            ->orderBy('Make', 'ASC')
            ->get();

        return $makes->isNotEmpty() ? $makes : false;
    }

    public function getModels($data)
    {
        $query = DB::connection($this->connection)
            ->table($this->table)
            ->select('Model')
            ->where('Make', $data['make'])
            ->groupBy('Model')
            ->orderBy('Model', 'ASC')
            ->get();

        return $query->isNotEmpty() ? $query : false;
    }

    public function getYears($data)
    {
        $query = DB::connection($this->connection)
            ->table($this->table)
            ->selectRaw('DISTINCT TRIM(Year) as Year') // Ensures unique and trimmed values
            ->where('Make', $data['make']);

        // Add condition for Model if provided
        if (!empty($data['model'])) {
            $query->where('Model', $data['model']);
        }

        // Fetch years as an array
        $years = $query->orderBy('Year', 'desc')->pluck('Year')->toArray();

        // Ensure all values are unique and properly formatted
        $yearString = implode('|', $years); // Convert to string with separator
        $years = explode('|', $yearString); // Convert back to array
        $years = array_map('trim', $years); // Trim any extra spaces
        $years = array_filter($years); // Remove empty values
        $years = array_unique($years); // Ensure unique values
        sort($years, SORT_NUMERIC); // Sort numerically

        return !empty($years) ? array_values($years) : false;
    }


    /**
     * Get variants based on make, model, and year
     *
     * @param array $data
     * @return array|bool
     */
    public function getVariants($data)
    {
        $query = DB::connection($this->connection)
            ->table($this->table)
            ->select('Variant')
            ->where('Make', $data['make'])
            ->where('Model', $data['model'])
            ->where('Year', 'like', '%' . $data['year'] . '%')
            ->groupBy('Variant')
            ->get();

        return $query->isNotEmpty() ? $query : false;
    }

    /**
     * Get engines based on make, model, year, and variant
     *
     * @param array $data
     * @return array|bool
     */
    public function getEngines($data)
    {
        $query = DB::connection($this->connection)
            ->table($this->table)
            ->select('Type')
            ->where('Make', $data['make'])
            ->where('Model', $data['model'])
            ->where('Year', 'like', '%' . $data['year'] . '%');

        if (!empty($data['variant'])) {
            $query->where('Variant', $data['variant']);
        }

        $engines = $query->groupBy('Type')
            ->get()
            ->pluck('Type')
            ->toArray();

        return !empty($engines) ? $engines : false;
    }

    /**
     * Get full vehicle information based on multiple filters
     *
     * @param array $data
     * @return object|bool
     */
    public function getVehicleInfo($data)
    {
        $query = DB::connection($this->connection)
            ->table($this->table)
            ->where('Make', $data['make'])
            ->where('Model', $data['model'])
            ->where('Year', 'like', '%' . $data['year'] . '%');

        if (!empty($data['variant'])) {
            $query->where('Variant', $data['variant']);
        }

        if (!empty($data['bodystyle'])) {
            $query->where('BodyStyle', $data['bodystyle']);
        }

        if (!empty($data['engine'])) {
            $query->where('Type', $data['engine']);
        }

        $vehicle = $query->first();

        return $vehicle ? $vehicle : false;
    }
}


/*// use SoftDeletes;
protected $table = 'vehiclevrmlist';
// Disable automatic timestamps
public $timestamps = false;

// Specify custom timestamp columns
const CREATED_AT = 'date_added';
const UPDATED_AT = 'date_modified';
protected $primaryKey = 'vehicle_id'; // If 'product_id' is the primary key

// Fillable fields for mass assignment
protected $fillable = [
    'vehicle_id',
    'Make',
    'Model',
    'Variant',
    'BodyStyle',
    'Type',
    'Year',
    'Engine',
    'K-Type',
    'Year2',
    'CC',
    'KW'
    // Add other fields as needed
];*/