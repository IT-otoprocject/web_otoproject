<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OdooApiService;
use Illuminate\Support\Facades\Log;

class ProductSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $search = $request->get('search');
            
            if (!$search || strlen($search) < 2) {
                return response()->json([]);
            }

            $configs = [
                'PIS' => [
                    'url' => 'https://odoo-pis.otoproject.id',
                    'db' => 'IOT-Odoo-Otoproject-PIS',
                    'username' => 'bod@otoproject.id',
                    'password' => 'autokeren',
                ],
                'MMI' => [
                    'url' => 'https://odoo-mmi.otoproject.id',
                    'db' => 'IOT-Odoo-Otoproject-MMI',
                    'username' => 'bod@otoproject.id',
                    'password' => 'autokeren',
                ],
            ];

            $allProducts = [];

            foreach ($configs as $key => $config) {
                try {
                    $odoo = new OdooApiService($config);
                    $domain = ['|', '|',
                        ['name', 'ilike', $search],
                        ['default_code', 'ilike', $search],
                        ['id', '=', is_numeric($search) ? (int)$search : 0]
                    ];
                    
                    $products = $odoo->getProducts(['name', 'default_code', 'list_price', 'id'], 0, 20, $domain);
                    
                    foreach ($products as $product) {
                        $product['database'] = $key;
                        $allProducts[] = $product;
                    }
                } catch (\Exception $e) {
                    // Log error but continue with other databases
                    Log::error("Error searching products in {$key}: " . $e->getMessage());
                }
            }

            // Sort by relevance
            usort($allProducts, function($a, $b) use ($search) {
                $searchLower = strtolower($search);
                $aNameLower = strtolower($a['name']);
                $bNameLower = strtolower($b['name']);
                
                return strcmp($aNameLower, $bNameLower);
            });

            // Limit results
            $allProducts = array_slice($allProducts, 0, 50);

            return response()->json($allProducts);

        } catch (\Exception $e) {
            Log::error('Product search error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search products'], 500);
        }
    }
}