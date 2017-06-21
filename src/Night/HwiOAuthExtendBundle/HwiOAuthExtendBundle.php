<?php

namespace Night\HwiOAuthExtendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HwiOAuthExtendBundle extends Bundle
{
    public function getParent()
    {
        return "HWIOAuthBundle";
    }
}
