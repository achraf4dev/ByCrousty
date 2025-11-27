<?php

namespace App\Http\Controllers\Api\v1;

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
            'content' => [
                'description' => 'ByCrousty es tu panadería de confianza, comprometida con la calidad y frescura de nuestros productos.',
                'mission' => 'Ofrecer productos de panadería frescos y deliciosos, elaborados con ingredientes de la más alta calidad.',
                'vision' => 'Ser la panadería preferida de nuestra comunidad, reconocida por la excelencia de nuestros productos.',
                'values' => [
                    'Calidad',
                    'Frescura',
                    'Servicio al cliente',
                    'Tradición',
                    'Innovación'
                ],
                'history' => 'Fundada en 2020, ByCrousty nace de la pasión por la panadería tradicional combinada con técnicas modernas.',
            ]
        ]);
    }

    /**
     * Get Contact information
     */
    public function contact()
    {
        return response()->json([
            'title' => 'Contacto',
            'contact_info' => [
                'email' => 'info@bycrousty.com',
                'phone' => '+34 123 456 789',
                'whatsapp' => '+34 123 456 789',
                'address' => 'Calle Example, 123, 28001 Madrid, España',
                'social_media' => [
                    'facebook' => 'https://facebook.com/bycrousty',
                    'instagram' => 'https://instagram.com/bycrousty',
                    'twitter' => 'https://twitter.com/bycrousty',
                ],
                'business_hours' => [
                    'monday_friday' => '7:00 - 20:00',
                    'saturday' => '8:00 - 14:00',
                    'sunday' => 'Cerrado',
                ],
            ]
        ]);
    }

    /**
     * Get Find Us (location) information
     */
    public function findUs()
    {
        return response()->json([
            'title' => 'Encuéntranos',
            'location' => [
                'address' => 'Calle Example, 123, 28001 Madrid, España',
                'coordinates' => [
                    'latitude' => 40.4168,
                    'longitude' => -3.7038,
                ],
                'map_url' => 'https://maps.google.com/?q=40.4168,-3.7038',
                'directions' => 'Cerca del metro Sol, salida 2. A 5 minutos caminando de Plaza Mayor.',
                'parking' => 'Parking público disponible en Calle Cercana, 45',
                'transport' => [
                    'metro' => 'Línea 1, 2, 3 - Estación Sol',
                    'bus' => 'Líneas 3, 50, 51, 52',
                ],
            ]
        ]);
    }
}
