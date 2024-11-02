<?php

declare(strict_types=1);

use Yiisoft\View\WebView;

/** @var WebView $this */
$this->setTitle('Backend');
?>


<div class="card mt-3 col-md-8">
    <div class="card-body">
        <h2 class="card-title">Welcome to backend</h2>
    </div>
</div>

<div class="card mt-3 col-md-8">
    <div class="card-body">
        Just example table
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
