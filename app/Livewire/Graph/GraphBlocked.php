<?php

declare(strict_types=1);

namespace App\Livewire\Graph;

use RuntimeException;

/** Interno do {@see GraphPage}: a tela está bloqueada (teto) e não consulta a base. */
final class GraphBlocked extends RuntimeException {}
