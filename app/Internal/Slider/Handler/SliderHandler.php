<?php

namespace App\Internal\Slider\Handler;

use App\Internal\Slider\Const\SliderConst;
use App\Internal\Slider\Usecase\SliderUsecase;

class SliderHandler extends SliderConst
{
    private $usecase;
    public function __construct(SliderUsecase $usecase)
    {
        $this->usecase = $usecase;
    }

    public function index() {}

    public function show() {}

    public function store() {}

    public function update() {}

    public function destroy() {}
}
