<aside class="main-sidebar <?= config('Boilerplate')->theme['sidebar']['border'] ? 'border-right' : ''?> sidebar-<?= config('Boilerplate')->theme['sidebar']['type'] ?>-<?= config('Boilerplate')->theme['sidebar']['links']['bg'] ?> elevation-<?= config('Boilerplate')->theme['sidebar']['shadow'] ?>">
    <a href="<?= route_to('/') ?>" class="brand-link <?= !empty(config('Boilerplate')->theme['sidebar']['brand']['bg']) ? 'bg-'.config('Boilerplate')->theme['sidebar']['brand']['bg'] : '' ?>">
        <img src="<?= base_url(config('Boilerplate')->theme['sidebar']['brand']['logo']['icon']) ?>" class="brand-image img-circle elevation-<?= config('Boilerplate')->theme['sidebar']['brand']['logo']['shadow'] ?>" style="opacity: .8">
        <span class="brand-text"><?= config('Boilerplate')->theme['sidebar']['brand']['logo']['text'] ?></span>
    </a>
    <div class="sidebar">

        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent <?= config('Boilerplate')->theme['sidebar']['compact'] ? 'nav-compact' : '' ?>" data-widget="treeview"
                role="menu" data-accordion="false">
                <?php foreach (menu() as $parent) { 
                    // Calculate if parent is open using segment-based precise matching
                    $isParentOpen = false;
                    
                    // Context-aware override: Force Inventoriu active if query parameter naran_pedidu is present (accessed from Inventoriu dashboard cards)
                    if ($parent->route === 'admin/inventoriu' && !empty(request()->getGet('naran_pedidu'))) {
                        $isParentOpen = true;
                    } elseif (current_url() == base_url($parent->route)) {
                        $isParentOpen = true;
                    } else {
                        // 1. Try segment-based matching on parent route itself
                        $cleanParentRoute = explode('?', $parent->route)[0];
                        $uriSegments = explode('/', uri_string());
                        $parentSegments = explode('/', $cleanParentRoute);
                        
                        if (count($uriSegments) === count($parentSegments)) {
                            $parentMatch = true;
                            for ($i = 0; $i < count($parentSegments); $i++) {
                                if ($uriSegments[$i] !== $parentSegments[$i]) {
                                    $parentMatch = false;
                                    break;
                                }
                            }
                            if ($parentMatch && strpos($parent->route, '?') !== false) {
                                $queryParams = [];
                                parse_str(explode('?', $parent->route)[1], $queryParams);
                                foreach ($queryParams as $k => $v) {
                                    if (request()->getGet($k) !== $v) {
                                        $parentMatch = false;
                                        break;
                                    }
                                }
                            }
                            if ($parentMatch && strpos($parent->route, '?') === false && !empty(request()->getGet())) {
                                $parentMatch = false;
                            }
                            if ($parentMatch) {
                                $isParentOpen = true;
                            }
                        }

                        // 2. If parent is still not open, check children
                        if (!$isParentOpen) {
                            foreach ($parent->children as $child) {
                                $cleanChildRoute = explode('?', $child->route)[0];
                                $childSegments = explode('/', $cleanChildRoute);
                                
                                if (count($uriSegments) >= count($childSegments)) {
                                    $match = true;
                                    for ($i = 0; $i < count($childSegments); $i++) {
                                        if ($uriSegments[$i] !== $childSegments[$i]) {
                                            $match = false;
                                            break;
                                        }
                                    }
                                    if ($match && strpos($child->route, '?') !== false) {
                                        $queryParams = [];
                                        parse_str(explode('?', $child->route)[1], $queryParams);
                                        foreach ($queryParams as $k => $v) {
                                            if (request()->getGet($k) !== $v) {
                                                $match = false;
                                                break;
                                            }
                                        }
                                    }
                                    if ($match && strpos($child->route, '?') === false && !empty(request()->getGet())) {
                                        $match = false;
                                    }
                                    if ($match) {
                                        $isParentOpen = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                ?>
                <li class="nav-item has-treeview <?= $isParentOpen ? 'menu-open' : '' ?>">
                    <a href="<?= base_url($parent->route) ?>" class="nav-link <?= $isParentOpen ? 'active' : '' ?>">
                        <i class="nav-icon <?= $parent->icon ?>"></i>
                        <p>
                            <?= $parent->title ?>
                            <?php if (count($parent->children)) { ?>
                                <i class="right fas fa-angle-left"></i>
                            <?php } ?>
                        </p>
                    </a>
                    <?php if (count($parent->children)) { ?>
                    <ul class="nav nav-treeview">
                        <?php foreach ($parent->children as $child) { 
                            // Calculate if child is active using segment-based precise matching
                            $isChildActive = false;
                            if (current_url() == base_url($child->route)) {
                                $isChildActive = true;
                            } else {
                                $cleanChildRoute = explode('?', $child->route)[0];
                                $uriSegments = explode('/', uri_string());
                                $childSegments = explode('/', $cleanChildRoute);
                                
                                if (count($uriSegments) >= count($childSegments)) {
                                    $match = true;
                                    for ($i = 0; $i < count($childSegments); $i++) {
                                        if ($uriSegments[$i] !== $childSegments[$i]) {
                                            $match = false;
                                            break;
                                        }
                                    }
                                    if ($match && strpos($child->route, '?') !== false) {
                                        $queryParams = [];
                                        parse_str(explode('?', $child->route)[1], $queryParams);
                                        foreach ($queryParams as $k => $v) {
                                            if (request()->getGet($k) !== $v) {
                                                $match = false;
                                                break;
                                            }
                                        }
                                    }
                                    if ($match && strpos($child->route, '?') === false && !empty(request()->getGet())) {
                                        $match = false;
                                    }
                                    if ($match) {
                                        $isChildActive = true;
                                    }
                                }
                            }
                        ?>
                        <li class="nav-item">
                            <a href="<?= base_url($child->route) ?>"
                                class="nav-link <?= $isChildActive ? 'active' : '' ?>">
                                <i class="nav-icon <?= $child->icon ?>"></i>
                                <p><?= $child->title ?></p>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</aside>