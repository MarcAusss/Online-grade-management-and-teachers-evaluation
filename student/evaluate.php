<?php
// Helper function for ordinal suffixes
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
        switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

// Fetch the academic year based on is_default or status
$academic_query = "
    SELECT * FROM academic_list 
    WHERE 
        (is_default = 1 OR status IN (0, 1)) 
    AND status != 2
";
$academic = $conn->query($academic_query)->fetch_assoc();

if ($academic) {
    $_SESSION['academic'] = array(
        'id' => $academic['id'],
        'year' => $academic['year'],
        'semester' => $academic['semester'],
        'status' => $academic['status']
    );
}

// Fetch distinct faculty, class, and subject for dropdowns
$faculty_query = "SELECT DISTINCT f.id as fid, CONCAT(f.firstname, ' ', f.lastname) as faculty FROM restriction_list r INNER JOIN faculty_list f ON f.id = r.faculty_id WHERE r.academic_id = {$_SESSION['academic']['id']}";
$faculty_list = $conn->query($faculty_query);

$class_query = "SELECT DISTINCT c.id as cid, CONCAT(c.curriculum, ' - ', c.level, ' ', c.section) as class FROM restriction_list r INNER JOIN class_list c ON c.id = r.class_id WHERE r.academic_id = {$_SESSION['academic']['id']}";
$class_list = $conn->query($class_query);

$subject_query = "SELECT DISTINCT s.id as sid, s.code as subject_code, s.subject FROM restriction_list r INNER JOIN subject_list s ON s.id = r.subject_id WHERE r.academic_id = {$_SESSION['academic']['id']}";
$subject_list = $conn->query($subject_query);
?>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-3">
            <form id="filter-evaluation">
                <div class="form-group">
                    <label for="faculty_id">Select Faculty</label>
                    <select id="faculty_id" name="faculty_id" class="form-control">
                        <option value="">-- Select Faculty --</option>
                        <?php while ($row = $faculty_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['fid']; ?>"><?php echo $row['faculty']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="class_id">Select Class</label>
                    <select id="class_id" name="class_id" class="form-control">
                        <option value="">-- Select Class --</option>
                        <?php while ($row = $class_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['cid']; ?>"><?php echo $row['class']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject_id">Select Subject</label>
                    <select id="subject_id" name="subject_id" class="form-control">
                        <option value="">-- Select Subject --</option>
                        <?php while ($row = $subject_list->fetch_assoc()): ?>
                            <option value="<?php echo $row['sid']; ?>"><?php echo $row['subject_code'].' - '.$row['subject']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Proceed to Evaluation</button>
            </form>
        </div>  

        <div class="col-md-9">
            <div id="evaluation-form" style="display:none;">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <b>Evaluation Questionnaire for Academic: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</b>
                        <div class="card-tools">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary mx-1" id="submit-evaluation">Submit Evaluation</button>

                        </div>
                    </div>
                    <div class="card-body">
                        <fieldset class="border border-info p-2 w-100">
                            <legend class="w-auto">Rating Legend</legend>
                            <p>5 = Strongly Agree, 4 = Agree, 3 = Uncertain, 2 = Disagree, 1 = Strongly Disagree</p>
                        </fieldset>
                        <form id="manage-evaluation">
                            <input type="hidden" name="class_id" id="hidden_class_id">
                            <input type="hidden" name="faculty_id" id="hidden_faculty_id">
                            <input type="hidden" name="subject_id" id="hidden_subject_id">
                            <input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">

                            <div id="questionnaire-section"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle evaluation form filter and load questions
$('#filter-evaluation').submit(function(e) {
    e.preventDefault();
    
    var faculty_id = $('#faculty_id').val();
    var class_id = $('#class_id').val();
    var subject_id = $('#subject_id').val();

    if (!faculty_id || !class_id || !subject_id) {
        alert("Please select faculty, class, and subject.");
        return;
    }

    // Set hidden fields for the form submission
    $('#hidden_faculty_id').val(faculty_id);
    $('#hidden_class_id').val(class_id);
    $('#hidden_subject_id').val(subject_id);

    // Load the evaluation form based on the selection
    $.ajax({
        url: 'ajax.php?action=fetch_questions',
        method: 'POST',
        data: {
            faculty_id: faculty_id,
            class_id: class_id,
            subject_id: subject_id
        },
        success: function(response) {
            $('#questionnaire-section').html(response);
            $('#evaluation-form').show();
        }
    });
});

// Submit the evaluation form
$('#manage-evaluation').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'ajax.php?action=save_evaluation',
        method: 'POST',
        data: $(this).serialize(),
  success: function(resp) {
    console.log(resp);  // Log the response for debugging
    if (resp == "1" || resp == 1) {  // Handle both numeric and string "1"
        alert_toast("Data successfully saved.", "success");
        setTimeout(function() {
            location.reload();
        }, 1750);
    } else {
        alert_toast("An error occurred while saving the data.", "error");
    }
}
    });
});

$('#submit-evaluation').click(function() {
    $('#manage-evaluation').submit();
});
</script>
