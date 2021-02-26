<?php

$reports = $args['reports'];

?>

<div class='container-wrapper'>
    <div class='container-header'>
        <h2>Reported posts</h2>
    </div>
    <div class='container-content'>
        <?php
        if (!empty($reports)) {
            echo "<table><tbody>";
            foreach ($reports as $report) {

                $report_id = $report['id'];
                $post_id = $report['post_id'];
                $thread_id = $report['thread_id'];
                $board_dir = $report['board_dir'];

                ?>
                <tr>
                    <td><a href='index.php?board/dir=<?php echo $board_dir; ?>/thread=<?php echo $thread_id; ?>#p<?php echo $post_id; ?>' class='text-link'>>><?php echo $post_id; ?></a></td>
                    <td><h4><?php echo $board_dir; ?></h4></td>
                    <td><p class='info-text'>thread no. <?php echo $thread_id; ?> post no. <?php echo $post_id; ?></p></td>
                    <td><a class='post-thread-header-text' id="report-<?php echo $report_id; ?>" onclick="deleteReport(this)">[Delete]</a></td>
                </tr>
                <?php

            }
            echo "</tbody></table>";

        } else {
            echo "<p>No reports yet.</p>";
        }

        ?>
    </div>
</div>