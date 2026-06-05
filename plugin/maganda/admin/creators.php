<?php
$sub_menu = '200920';
require_once __DIR__ . '/_common.php';

$table = maganda_table('creator');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'save') {
        $mc_id = isset($_POST['mc_id']) ? (int) $_POST['mc_id'] : 0;
        $fields = array(
            'mc_slug' => trim($_POST['mc_slug']),
            'mc_name' => trim($_POST['mc_name']),
            'mc_title' => trim($_POST['mc_title']),
            'mc_category' => trim($_POST['mc_category']),
            'mc_intro' => trim($_POST['mc_intro']),
            'mc_stream_url' => trim($_POST['mc_stream_url']),
            'mc_youtube_id' => trim($_POST['mc_youtube_id']),
            'mc_is_live' => isset($_POST['mc_is_live']) ? 1 : 0,
            'mc_viewers' => (int) $_POST['mc_viewers'],
            'mc_sort' => (int) $_POST['mc_sort'],
            'mc_enabled' => isset($_POST['mc_enabled']) ? 1 : 0,
        );

        if ($fields['mc_slug'] === '' || $fields['mc_name'] === '') {
            alert('슬러그와 이름은 필수입니다.');
        }

        if ($mc_id > 0) {
            $sets = array();
            foreach ($fields as $key => $value) {
                $sets[] = "`{$key}` = '" . sql_escape_string($value) . "'";
            }
            sql_query(" UPDATE `{$table}` SET " . implode(', ', $sets) . " WHERE `mc_id` = {$mc_id} ");
        } else {
            sql_query(
                " INSERT INTO `{$table}`
                    (`mc_slug`,`mc_name`,`mc_title`,`mc_category`,`mc_intro`,`mc_stream_url`,`mc_youtube_id`,`mc_is_live`,`mc_viewers`,`mc_sort`,`mc_enabled`,`mc_datetime`)
                  VALUES
                    ('" . sql_escape_string($fields['mc_slug']) . "','" . sql_escape_string($fields['mc_name']) . "','" . sql_escape_string($fields['mc_title']) . "','" . sql_escape_string($fields['mc_category']) . "','" . sql_escape_string($fields['mc_intro']) . "','" . sql_escape_string($fields['mc_stream_url']) . "','" . sql_escape_string($fields['mc_youtube_id']) . "'," . (int) $fields['mc_is_live'] . "," . (int) $fields['mc_viewers'] . "," . (int) $fields['mc_sort'] . "," . (int) $fields['mc_enabled'] . ",'" . G5_TIME_YMDHIS . "') "
            );
        }

        goto_url(G5_PLUGIN_URL . '/maganda/admin/creators.php');
    }
}

$edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$edit = $edit_id ? sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_id` = {$edit_id} ") : null;
$list = array();
$result = sql_query(" SELECT * FROM `{$table}` ORDER BY `mc_sort` ASC, `mc_id` ASC ");
while ($row = sql_fetch_array($result)) {
    $list[] = $row;
}

$g5['title'] = '방송회원 관리';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<div class="local_ov01 local_ov">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/index.php">마간다TV</a> &gt; 방송회원
</div>

<h2 class="h2_frm">방송회원 목록</h2>
<div class="tbl_head01 tbl_wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>슬러그</th>
                <th>이름</th>
                <th>LIVE</th>
                <th>정렬</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($list)) { ?>
            <tr><td colspan="6">등록된 방송회원이 없습니다.</td></tr>
        <?php } else { foreach ($list as $row) { ?>
            <tr>
                <td><?php echo (int) $row['mc_id']; ?></td>
                <td><?php echo get_text($row['mc_slug']); ?></td>
                <td><?php echo get_text($row['mc_name']); ?></td>
                <td><?php echo (int) $row['mc_is_live'] === 1 ? 'ON' : 'OFF'; ?></td>
                <td><?php echo (int) $row['mc_sort']; ?></td>
                <td><a href="?id=<?php echo (int) $row['mc_id']; ?>">수정</a></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>

<h2 class="h2_frm"><?php echo $edit ? '방송회원 수정' : '방송회원 추가'; ?></h2>
<form method="post">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="mc_id" value="<?php echo $edit ? (int) $edit['mc_id'] : 0; ?>">
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <tbody>
                <tr><th>슬러그</th><td><input type="text" name="mc_slug" value="<?php echo $edit ? get_text($edit['mc_slug']) : ''; ?>" class="frm_input" required></td></tr>
                <tr><th>이름</th><td><input type="text" name="mc_name" value="<?php echo $edit ? get_text($edit['mc_name']) : ''; ?>" class="frm_input" required></td></tr>
                <tr><th>방송 제목</th><td><input type="text" name="mc_title" value="<?php echo $edit ? get_text($edit['mc_title']) : ''; ?>" class="frm_input" style="width:100%"></td></tr>
                <tr><th>카테고리</th><td><input type="text" name="mc_category" value="<?php echo $edit ? get_text($edit['mc_category']) : ''; ?>" class="frm_input"></td></tr>
                <tr><th>소개</th><td><input type="text" name="mc_intro" value="<?php echo $edit ? get_text($edit['mc_intro']) : ''; ?>" class="frm_input" style="width:100%"></td></tr>
                <tr><th>YouTube URL</th><td><input type="text" name="mc_stream_url" value="<?php echo $edit ? get_text($edit['mc_stream_url']) : ''; ?>" class="frm_input" style="width:100%"></td></tr>
                <tr><th>YouTube ID</th><td><input type="text" name="mc_youtube_id" value="<?php echo $edit ? get_text($edit['mc_youtube_id']) : ''; ?>" class="frm_input"></td></tr>
                <tr><th>시청자 수</th><td><input type="number" name="mc_viewers" value="<?php echo $edit ? (int) $edit['mc_viewers'] : 0; ?>" class="frm_input"></td></tr>
                <tr><th>정렬</th><td><input type="number" name="mc_sort" value="<?php echo $edit ? (int) $edit['mc_sort'] : 0; ?>" class="frm_input"></td></tr>
                <tr><th>옵션</th><td>
                    <label><input type="checkbox" name="mc_is_live" value="1" <?php echo $edit && (int) $edit['mc_is_live'] === 1 ? 'checked' : ''; ?>> LIVE ON</label>
                    <label><input type="checkbox" name="mc_enabled" value="1" <?php echo !$edit || (int) $edit['mc_enabled'] === 1 ? 'checked' : ''; ?>> 사용</label>
                </td></tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="저장" class="btn_submit">
        <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php" class="btn_frmline">새로 추가</a>
    </div>
</form>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
