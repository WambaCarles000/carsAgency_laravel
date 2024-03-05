<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class PaginationController extends Controller{


    public function paginateResults(Collection $data, $page, $limit)
    
    {
        $total_items = $data->count();
        $offset = ($page - 1) * $limit;

        // Extraire la portion des résultats pour la page actuelle
        $paged_data = $data->slice($offset, $limit)->values();

        // Calculer le nombre total de pages nécessaires pour afficher tous les éléments
        $total_pages = ceil($total_items / $limit);

        return [
            'current_page' => $page,
            'limit' => $limit,
            'total_pages' => $total_pages,
            'data' => $paged_data,
        ];
    }

}