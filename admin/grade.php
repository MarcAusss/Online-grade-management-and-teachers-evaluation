<table>
  <thead>
    <tr>
      <th>Student</th>
      <th>Subject</th>
      <th>Grade</th>
      <th>Submitted By</th>
      <th>Submitted On</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $grades = mysqli_query($conn, "SELECT g.*, s.name as student_name, sub.subject_name, f.name as faculty_name 
                                     FROM grades g
                                     JOIN students s ON g.student_id = s.id
                                     JOIN subject_list sub ON g.subject_id = sub.id
                                     JOIN faculty_list f ON g.faculty_id = f.id");
      while($row = mysqli_fetch_assoc($grades)) {
        echo "<tr>
                <td>{$row['student_name']}</td>
                <td>{$row['subject_name']}</td>
                <td>{$row['grade']}</td>
                <td>{$row['faculty_name']}</td>
                <td>{$row['timestamp']}</td>
              </tr>";
      }
    ?>
  </tbody>
</table>
