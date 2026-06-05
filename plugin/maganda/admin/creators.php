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

        $fields = maganda_normalize_stream_fields($fields);

        if ($fields['mc_is_live'] === 1 && $fields['mc_youtube_id'] === '') {
            alert('LIVE ON 상태에서는 YouTube 라이브 URL을 입력해 주세요.');
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

        goto_url(G5_PLUGIN_URL . '/maganda/admin/creators.php' . ($mc_id > 0 ? '?id=' . $mc_id : ''));
    }
}

$edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$edit = $edit_id ? sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_id` = {$edit_id} ") : null;
$list = array();
$result = sql_query(" SELECT * FROM `{$table}` ORDER BY `mc_sort` ASC, `mc_id` ASC ");
while ($row = sql_fetch_array($result)) {
    $list[] = $row;
}

$preview_id = $edit ? maganda_stream_video_id($edit) : '';
$preview_embed = $preview_id !== '' ? maganda_youtube_embed_url($preview_id, false) : '';

$g5['title'] = '방송회원 관리';
include_once G5_ADMIN_PATH . '/admin.head.php';
?>
<style>
.mg-stream-field { max-width: 720px; }
.mg-stream-help { margin: 8px 0 0; color: #666; font-size: 12px; line-height: 1.6; }
.mg-stream-preview { margin-top: 16px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #0f172a; }
.mg-stream-preview__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; color: #fff; }
.mg-stream-preview__frame { position: relative; width: 100%; max-width: 640px; aspect-ratio: 16 / 9; background: #111; border-radius: 10px; overflow: hidden; }
.mg-stream-preview__frame iframe { width: 100%; height: 100%; border: 0; }
.mg-stream-preview__empty { display: flex; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-size: 13px; text-align: center; padding: 16px; }
.mg-stream-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
.mg-stream-badge--live { background: #dc2626; color: #fff; }
.mg-stream-badge--off { background: #334155; color: #e2e8f0; }
.mg-stream-id { margin-top: 10px; font-size: 12px; color: #cbd5e1; }
</style>

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
                <th>YouTube</th>
                <th>LIVE</th>
                <th>정렬</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($list)) { ?>
            <tr><td colspan="7">등록된 방송회원이 없습니다.</td></tr>
        <?php } else { foreach ($list as $row) {
            $yt_id = maganda_stream_video_id($row);
        ?>
            <tr>
                <td><?php echo (int) $row['mc_id']; ?></td>
                <td><?php echo get_text($row['mc_slug']); ?></td>
                <td><?php echo get_text($row['mc_name']); ?></td>
                <td>
                    <?php if ($yt_id !== '') { ?>
                        <a href="<?php echo get_text($row['mc_stream_url']); ?>" target="_blank" rel="noopener"><?php echo get_text($yt_id); ?></a>
                    <?php } else { ?>
                        <span class="txt_gray">미연결</span>
                    <?php } ?>
                </td>
                <td><?php echo (int) $row['mc_is_live'] === 1 ? '<span style="color:#dc2626;font-weight:700;">ON</span>' : 'OFF'; ?></td>
                <td><?php echo (int) $row['mc_sort']; ?></td>
                <td><a href="?id=<?php echo (int) $row['mc_id']; ?>">수정</a></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>

<h2 class="h2_frm"><?php echo $edit ? '방송회원 수정' : '방송회원 추가'; ?></h2>
<form method="post" id="mgCreatorForm">
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="mc_id" value="<?php echo $edit ? (int) $edit['mc_id'] : 0; ?>">
    <input type="hidden" name="mc_youtube_id" id="mc_youtube_id" value="<?php echo $edit ? get_text($edit['mc_youtube_id']) : ''; ?>">
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <tbody>
                <tr><th>슬러그</th><td><input type="text" name="mc_slug" value="<?php echo $edit ? get_text($edit['mc_slug']) : ''; ?>" class="frm_input" required></td></tr>
                <tr><th>이름</th><td><input type="text" name="mc_name" value="<?php echo $edit ? get_text($edit['mc_name']) : ''; ?>" class="frm_input" required></td></tr>
                <tr><th>방송 제목</th><td><input type="text" name="mc_title" value="<?php echo $edit ? get_text($edit['mc_title']) : ''; ?>" class="frm_input mg-stream-field"></td></tr>
                <tr><th>카테고리</th><td><input type="text" name="mc_category" value="<?php echo $edit ? get_text($edit['mc_category']) : ''; ?>" class="frm_input"></td></tr>
                <tr><th>소개</th><td><input type="text" name="mc_intro" value="<?php echo $edit ? get_text($edit['mc_intro']) : ''; ?>" class="frm_input mg-stream-field"></td></tr>
                <tr>
                    <th>YouTube 라이브 URL</th>
                    <td>
                        <input type="url" name="mc_stream_url" id="mc_stream_url" value="<?php echo $edit ? get_text($edit['mc_stream_url']) : ''; ?>" class="frm_input mg-stream-field" placeholder="https://www.youtube.com/watch?v=Jav-pWT70rg">
                        <p class="mg-stream-help">
                            YouTube 라이브/일반 영상 URL을 붙여넣으면 홈페이지 방송룸에 자동 재생됩니다.<br>
                            지원 형식: <code>watch?v=</code>, <code>youtu.be/</code>, <code>/live/</code>, <code>/shorts/</code>
                        </p>
                        <div class="mg-stream-preview">
                            <div class="mg-stream-preview__head">
                                <strong>방송 미리보기</strong>
                                <span id="mgStreamBadge" class="mg-stream-badge <?php echo $edit && (int) $edit['mc_is_live'] === 1 ? 'mg-stream-badge--live' : 'mg-stream-badge--off'; ?>">
                                    <?php echo $edit && (int) $edit['mc_is_live'] === 1 ? 'LIVE ON' : 'LIVE OFF'; ?>
                                </span>
                            </div>
                            <div class="mg-stream-preview__frame">
                                <?php if ($preview_embed !== '') { ?>
                                    <iframe id="mgStreamPreviewFrame" src="<?php echo htmlspecialchars($preview_embed, ENT_QUOTES, 'UTF-8'); ?>" allow="encrypted-media; picture-in-picture" allowfullscreen></iframe>
                                <?php } else { ?>
                                    <div id="mgStreamPreviewEmpty" class="mg-stream-preview__empty">YouTube URL을 입력하면 미리보기가 표시됩니다.</div>
                                    <iframe id="mgStreamPreviewFrame" style="display:none;" allow="encrypted-media; picture-in-picture" allowfullscreen></iframe>
                                <?php } ?>
                            </div>
                            <div class="mg-stream-id">영상 ID: <span id="mgStreamVideoId"><?php echo $preview_id !== '' ? get_text($preview_id) : '-'; ?></span></div>
                        </div>
                    </td>
                </tr>
                <tr><th>시청자 수</th><td><input type="number" name="mc_viewers" value="<?php echo $edit ? (int) $edit['mc_viewers'] : 0; ?>" class="frm_input"></td></tr>
                <tr><th>정렬</th><td><input type="number" name="mc_sort" value="<?php echo $edit ? (int) $edit['mc_sort'] : 0; ?>" class="frm_input"></td></tr>
                <tr><th>옵션</th><td>
                    <label><input type="checkbox" name="mc_is_live" id="mc_is_live" value="1" <?php echo $edit && (int) $edit['mc_is_live'] === 1 ? 'checked' : ''; ?>> LIVE ON (홈 “지금 라이브 중” 노출)</label>
                    <label><input type="checkbox" name="mc_enabled" value="1" <?php echo !$edit || (int) $edit['mc_enabled'] === 1 ? 'checked' : ''; ?>> 사용</label>
                </td></tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="저장" class="btn_submit">
        <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php" class="btn_frmline">새로 추가</a>
        <?php if ($edit && $preview_id !== '') { ?>
            <a href="<?php echo G5_URL; ?>/?#mg-live" target="_blank" class="btn_frmline">홈페이지에서 보기</a>
        <?php } ?>
    </div>
</form>
<script>
(function () {
    var urlInput = document.getElementById('mc_stream_url');
    var idInput = document.getElementById('mc_youtube_id');
    var idLabel = document.getElementById('mgStreamVideoId');
    var frame = document.getElementById('mgStreamPreviewFrame');
    var empty = document.getElementById('mgStreamPreviewEmpty');
    var liveInput = document.getElementById('mc_is_live');
    var badge = document.getElementById('mgStreamBadge');

    function extractId(value) {
        value = (value || '').trim();
        if (/^[a-zA-Z0-9_-]{11}$/.test(value)) {
            return value;
        }
        var patterns = [
            /(?:v=|\/vi\/|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_-]{11})/,
            /[?&]v=([a-zA-Z0-9_-]{11})/
        ];
        for (var i = 0; i < patterns.length; i++) {
            var match = value.match(patterns[i]);
            if (match) {
                return match[1];
            }
        }
        return '';
    }

    function syncPreview() {
        var id = extractId(urlInput.value) || extractId(idInput.value);
        idInput.value = id;
        idLabel.textContent = id || '-';

        if (!id) {
            frame.style.display = 'none';
            frame.removeAttribute('src');
            if (empty) {
                empty.style.display = 'flex';
            }
            return;
        }

        if (empty) {
            empty.style.display = 'none';
        }
        frame.style.display = 'block';
        frame.src = 'https://www.youtube.com/embed/' + id + '?rel=0&modestbranding=1&playsinline=1';
    }

    function syncBadge() {
        if (!badge) {
            return;
        }
        if (liveInput.checked) {
            badge.textContent = 'LIVE ON';
            badge.className = 'mg-stream-badge mg-stream-badge--live';
        } else {
            badge.textContent = 'LIVE OFF';
            badge.className = 'mg-stream-badge mg-stream-badge--off';
        }
    }

    urlInput.addEventListener('input', syncPreview);
    urlInput.addEventListener('change', syncPreview);
    if (liveInput) {
        liveInput.addEventListener('change', syncBadge);
    }
    syncPreview();
})();
</script>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
