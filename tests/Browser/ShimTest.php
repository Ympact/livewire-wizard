<?php

it('shim browser test', function () {
    $this->view('welcome')->assertSee('Laravel');
});