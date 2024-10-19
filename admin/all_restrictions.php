<?php 
include __DIR__ . '/../db_connect.php';

?>

<div class="wrapper">
    <div class="content-wrapper">

        <section class="content">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Restrictions List</h3>
                    <a href="http://localhost/php/eval/index.php?page=manage_questionnaire&id=4" class="btn btn-sm btn-primary">Back to Questionnaire</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Faculty</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Pagination logic
                            $limit = 10; // Entries per page
                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                            $start = ($page - 1) * $limit;
                            
                            $query = "SELECT * FROM restriction_list ORDER BY id DESC LIMIT $start, $limit";
                            $result = $conn->query($query);

                            $count = $start + 1; // For row numbering
                            while ($row = $result->fetch_assoc()) {
                                $faculty = $conn->query("SELECT concat(firstname, ' ', lastname) as name FROM faculty_list WHERE id = {$row['faculty_id']}")->fetch_assoc();
                                $class = $conn->query("SELECT concat(curriculum, ' ', level, ' - ', section) as class FROM class_list WHERE id = {$row['class_id']}")->fetch_assoc();
                                $subject = $conn->query("SELECT concat(code, ' - ', subject) as subj FROM subject_list WHERE id = {$row['subject_id']}")->fetch_assoc();
                            ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo $faculty['name'] ?? ''; ?></td>
                                <td><?php echo $class['class'] ?? ''; ?></td>
                                <td><?php echo $subject['subj'] ?? ''; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger" onclick="deleteRestriction(<?php echo $row['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <?php
                    $resultTotal = $conn->query("SELECT COUNT(*) AS total FROM restriction_list");
                    $total = $resultTotal->fetch_assoc()['total'];
                    $pages = ceil($total / $limit);
                    ?>
                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $pages; $i++) : ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="all_restrictions.php?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
    </div>
</div>



<script>
    function deleteRestriction(id) {
        if (confirm("Are you sure you want to delete this restriction?")) {
            $.ajax({
                url: 'ajax.php?action=delete_restriction',
                method: 'POST',
                data: { id: id },
                success: function(resp) {
                    if (resp == 1) {
                        alert("Restriction deleted successfully.");
                        location.reload();
                    } else {
                        alert("Failed to delete restriction.");
                    }
                }
            });
        }
    }
</script>
