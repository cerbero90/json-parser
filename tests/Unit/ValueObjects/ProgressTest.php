<?php

use Cerbero\JsonParser\ValueObjects\Progress;


it('tracks the progress', function () {
    $progress = new Progress();

    expect($progress)
        ->current()->toBe(0)
        ->total()->toBeNull()
        ->format()->toBeNull()
        ->percentage()->toBeNull()
        ->fraction()->toBeNull();

    $progress->setTotal(200)->setCurrent(33);

    expect($progress)
        ->current()->toBe(33)
        ->total()->toBe(200)
        ->format()->toBe('16.5%')
        ->percentage()->toBe(16.5)
        ->fraction()->toBe(0.165);
});
