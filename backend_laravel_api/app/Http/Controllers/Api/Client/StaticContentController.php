<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaticContentController extends Controller
{
    /**
     * Get About Us content
     */
    public function about()
    {
        return response()->json([
            'title' => 'Sobre Nosotros',
            'content' => 'ByCrousty es tu panadería de confianza. Desde 1990, horneamos pan fresco cada día con ingredientes de la más alta calidad. Nuestra pasión por la panadería tradicional se combina con innovación para ofrecerte los mejores productos.',
            'image' => null,
        ]);
    }

    /**
     * Get Contact information
     */
    public function contact()
    {
        return response()->json([
            'title' => 'Contacto',
            'phone' => '+34 123 456 789',
            'email' => 'info@bycrousty.com',
            'address' => 'Calle Principal 123, 28001 Madrid, España',
            'hours' => [
                'Lunes a Viernes' => '7:00 - 21:00',
                'Sábados' => '8:00 - 21:00',
                'Domingos' => '8:00 - 14:00',
            ],
        ]);
    }

    /**
     * Get Find Us (location) information
     */
    public function findUs()
    {
        return response()->json([
            'title' => 'Encuéntranos',
            'address' => 'Calle Principal 123, 28001 Madrid, España',
            'coordinates' => [
                'lat' => 40.4168,
                'lng' => -3.7038,
            ],
            'directions' => 'Estamos ubicados en el corazón de Madrid, cerca de la Plaza Mayor. Puedes llegar en Metro (Línea 1, Estación Sol) o en autobús (líneas 3, 50, 51).',
        ]);
    }
}
