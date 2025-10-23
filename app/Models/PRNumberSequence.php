<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PRNumberSequence extends Model
{
    protected $table = 'pr_number_sequences';
    
    protected $fillable = [
        'prefix',
        'date', 
        'last_sequence'
    ];
    
    protected $casts = [
        'date' => 'date',
        'last_sequence' => 'integer'
    ];
    
    /**
     * Get next sequence number untuk prefix dan tanggal tertentu
     * Menggunakan atomic increment untuk prevent race condition
     */
    public static function getNextSequence($prefix, $date)
    {
        return DB::transaction(function() use ($prefix, $date) {
            // Try to increment existing record
            $updated = DB::table('pr_number_sequences')
                ->where('prefix', $prefix)
                ->where('date', $date)
                ->increment('last_sequence');
            
            if ($updated) {
                // Get the incremented value
                $sequence = DB::table('pr_number_sequences')
                    ->where('prefix', $prefix)
                    ->where('date', $date)
                    ->value('last_sequence');
                    
                return $sequence;
            } else {
                // Create new record if doesn't exist
                DB::table('pr_number_sequences')->insert([
                    'prefix' => $prefix,
                    'date' => $date,
                    'last_sequence' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                return 1;
            }
        });
    }
}
