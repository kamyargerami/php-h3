<?php

namespace MichaelLindahl\H3;

use FFI;

trait MiscTrait
{
    public function degsToRads(float $degrees): float
    {
        if (php_sapi_name() !== 'cli') {
            return (new \Preloaded_H3())->degsToRads($degrees);
        }

        $ffi = FFI::cdef('double degsToRads(double degrees);', $this->lib);

        return $ffi->degsToRads($degrees);
    }

    public function radsToDegs(float $radians): float
    {
        if (php_sapi_name() !== 'cli') {
            return (new \Preloaded_H3())->radsToDegs($radians);
        }

        $ffi = FFI::cdef('double radsToDegs(double degrees);', $this->lib);

        return $ffi->radsToDegs($radians);
    }
}
