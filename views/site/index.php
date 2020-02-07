<div id="primaryCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#primaryCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#primaryCarousel" data-slide-to="1"></li>
        <li data-target="#primaryCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="d-block w-100 bg-info" style="height: 200px"></div>
            <div class="carousel-caption d-none d-md-block">
                <h5>First slide label</h5>
                <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-block w-100 bg-secondary" style="height: 200px"></div>
            <div class="carousel-caption d-none d-md-block">
                <h5>Second slide label</h5>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-block w-100 bg-dark" style="height: 200px"></div>
            <div class="carousel-caption d-none d-md-block">
                <h5>Third slide label</h5>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#primaryCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#primaryCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>


<div class="card mt-3 col-md-6">
    <div class="card-body">
        <h2 class="card-title">Console</h2>
        <?php $binPath = strtr('./vendor/bin/yii', '/', DIRECTORY_SEPARATOR); ?>
        <h4 class="card-title text-muted">Create new user</h4>
        <div>
            <code><?php echo "{$binPath} user/create &lt;login&gt; &lt;password&gt;" ?></code>
        </div>
        <h4 class="card-title text-muted">Add random content</h4>
        <div>
            <code><?php echo "{$binPath} fixture/add [count = 10]" ?></code>
        </div>
        <h4 class="card-title text-muted">Migrations</h4>
        <div>
                <code><?php echo "{$binPath} migrate/create" ?></code>
            <br><code><?php echo "{$binPath} migrate/generate" ?></code>
            <br><code><?php echo "{$binPath} migrate/up" ?></code>
            <br><code><?php echo "{$binPath} migrate/down" ?></code>
            <br><code><?php echo "{$binPath} migrate/list" ?></code>
        </div>
        <h4 class="card-title text-muted">DB Schema</h4>
        <div>
            <code><?php echo "{$binPath} cycle/schema" ?></code>
        </div>
    </div>
</div>
