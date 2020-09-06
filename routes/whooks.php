<?php

Route::post('paddle', 'PaddleController')->middleware('iprel:services.paddle');
