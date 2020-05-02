
<form action="upload.php" method="POST" enctype="multipart/form-data">
    <label for="imageUpload">Upload an profile image</label>
    <input type="file" name="avatars[]" multiple="multiple" />
    <button type="submit" value="Upload">Upload</button>
</form>

<?php
if(!empty($_FILES['avatars']['name'][0])) {
    $files = $_FILES['avatars'];

    $uploaded = array();
    $failed = array();

    $allowed = array('png', 'jpg', 'jpeg', 'gif');

    foreach($files['name'] as $position => $file_name) {
        $file_tmp = $files['tmp_name'][$position];
        $file_size = $files['size'][$position];
        $file_error =$files['error'][$position];

        $file_ext = explode('.', $file_name);
        $file_ext = strtolower(end($file_ext));

        if(in_array($file_ext, $allowed)) {
            if($file_error === 0) {
                if($file_size <= 1000000) {
                    $file_name_id = uniqid('', true) .'.' .$file_ext;
                    $file_destination = 'public/uploads/' .$file_name_id;

                    if(move_uploaded_file($file_tmp, $file_destination)) {
                        $uploaded[$position] = $file_destination;
                    } else {
                        $failed[$position] = "[{$file_name}] failed to upload.";
                    }

                } else {
                    $failed[$position] = "[{$file_name}] is too large.";
                }

            } else {
                $failed[$position] = "[{$file_name}] failed to upload. Error code : {$file_error}";
                    if($file_error === 1) {
                        $failed[$position] = "[{$file_name}] is too large.";
                    }
            }

        } else {
            $failed[$position] = "[{$file_name}] file extension '{$file_ext}' is not allowed.";
        }
        var_dump($file_ext);
    }

    if(!empty($uploaded)) {
        echo "<br><br>";
        echo "Successfully Uploaded 😏" ."<br>";
        foreach ($uploaded as $key => $value) {
            echo "<figure><img src='{$value}' /><figcaption>$value</figcaption>" ."<form action='upload.php' method='POST'><button name='submit' type='submit'>Delete File</button></form></figure>" .'<br>';
                if(isset($_POST['submit']) && (file_exists($file_destination))) {
                    unlink($file_destination);
                    exit();
                }
        }
        echo "<br>";
    }

    if(!empty($failed)) {
        echo "Failed to upload 😭😭😭" .'<br>';
        foreach ($failed as $key => $value) {
            echo $value ."<br>";
        }
    }
}