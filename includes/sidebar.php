 <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <!-- <li class="nav-item">
            <a class="nav-link" href="manage_pengelola.php">
                <span class="menu-title">Manage Pengelola</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li> -->

        <li class="nav-item">
            <a class="nav-link" href="new_visitor.php">
                <span class="menu-title">Input Data Wisata</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="hasil_predik.php">
                <span class="menu-title">Hasil Predik</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="manage_visitor.php">
                <span class="menu-title">Data Tempat Wisata</span>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
        </li>
        <?php
        $aid=$_SESSION['odmsaid'];
        $sql="SELECT * from  tbladmin where ID=:aid";
        $query = $dbh -> prepare($sql);
        $query->bindParam(':aid',$aid,PDO::PARAM_STR);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        $cnt=1;
        if($query->rowCount() > 0)
        {  
            foreach($results as $row)
            { 
                if($row->AdminName=="Admin"  )
                { 
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="false" aria-controls="general-pages">
                            <span class="menu-title" href="userregister.php">Management Wisata</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                        </a>
                        <div class="collapse" id="general-pages">

                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link" href="userregister.php">Register Wisata </a></li>
                                <li class="nav-item"> <a class="nav-link" href="user_permission.php"> User permissions</a></li>
                            </ul>
                        </div>
                    </li>
                    <?php 
                } 
            }
        } ?> 
        
 </ul>
</nav>