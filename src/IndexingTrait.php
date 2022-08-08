<?php

namespace MichaelLindahl\H3;

use FFI;

trait IndexingTrait
{
    public function geoToH3(float $lat, float $lon, int $res): string
    {
        if (php_sapi_name() !== 'cli') {
            return (new \Preloaded_H3())->geoToH3($lat, $lon, $res);
        }

        $ffi = FFI::cdef(
            self::H3IndexTypeDef.self::GeoCoordTypeDef.
            'H3Index geoToH3(const GeoCoord *g, int res);',
            $this->lib
        );

        $location = $ffi->new('GeoCoord');
        $location->lat = deg2rad($lat);
        $location->lon = deg2rad($lon);

        $h3 = $ffi->geoToH3(FFI::addr($location), $res);

        return dechex($h3);
    }

    public function h3ToGeo(string $h3Index): object
    {
        if (php_sapi_name() !== 'cli') {
            return (new \Preloaded_H3())->h3ToGeo($h3Index);
        }

        $ffi = FFI::cdef(
            self::H3IndexTypeDef.self::GeoCoordTypeDef.
            'void h3ToGeo(H3Index h3, GeoCoord *g);',
            $this->lib
        );

        $dec = hexdec($h3Index);
        $geoCord = $ffi->new('GeoCoord');
        $ffi->h3ToGeo($dec, FFI::addr($geoCord));

        return (object) [
            'lat' => rad2deg($geoCord->lat),
            'lon' => rad2deg($geoCord->lon),
        ];
    }

    public function h3ToGeoBoundary(string $h3Index): array
    {
        if (php_sapi_name() !== 'cli') {
            return (new \Preloaded_H3())->h3ToGeoBoundary($h3Index);
        }
    
        $ffi = FFI::cdef(
            self::H3IndexTypeDef.self::GeoCoordTypeDef.self::GeoBoundaryTypeDef.
            'void h3ToGeoBoundary(H3Index h3, GeoBoundary *gb);',
            $this->lib
        );

        $dec = hexdec($h3Index);
        $geoBoundary = $ffi->new('GeoBoundary');
        $ffi->h3ToGeoBoundary($dec, FFI::addr($geoBoundary));

        $array = [];
        for ($x = 0; $x <= count($geoBoundary->verts) / 2; $x++) {
            $geoCord = $geoBoundary->verts[$x];

            $array[] = (object) [
                'lat' => rad2deg($geoCord->lat),
                'lon' => rad2deg($geoCord->lon),
            ];
        }

        return $array;
    }
}
