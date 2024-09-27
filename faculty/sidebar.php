<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
        <a href="./" class="brand-link">
            <h3 class="text-center p-0 m-0"><b>Faculty Panel</b></h3>
        </a>
    </div>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item dropdown">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=result" class="nav-link nav-result">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>
                            Evaluation Result
                        </p>
                    </a>
                </li>
                <!-- New Grade Section -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=grade" class="nav-link nav-grade">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>
                            Grade
                        </p>
                    </a> <!-- Fixed missing closing tag here -->
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    $(document).ready(function(){
        var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
        var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
        
        // Append the suffix if necessary
        if(s != '')
            page = page + '_' + s;

        // Make the corresponding menu item active
        if($('.nav-link.nav-' + page).length > 0){
            $('.nav-link.nav-' + page).addClass('active');
            
            // If the link is inside a treeview, expand the menu
            if($('.nav-link.nav-' + page).hasClass('tree-item')){
                $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active');
                $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
            }

            // If the link is a treeview parent, open the tree
            if($('.nav-link.nav-' + page).hasClass('nav-is-tree')){
                $('.nav-link.nav-' + page).parent().addClass('menu-open');
            }
        }
    });
</script>
