<?php
$sub_menu = '200920';
require_once __DIR__ . '/_common.php';

$table = maganda_table('creator');
$form_errors = array();
$flash_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'apply_sample') {
        $sample_id = maganda_apply_sample_live_creator();
        alert(
            '샘플 방송회원 Jolie (졸리)에 YouTube LIVE가 연결되었습니다.\\nURL: ' . MAGANDA_SAMPLE_LIVE_URL,
            G5_PLUGIN_URL . '/maganda/admin/creators.php?id=' . $sample_id . '&saved=1'
        );
    }

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

        $fields = maganda_normalize_stream_fields($fields);
        $form_errors = maganda_validate_creator_fields($fields, $mc_id);

        if (!empty($form_errors)) {
            $edit = array_merge($fields, array('mc_id' => $mc_id));
            $edit_id = $mc_id;
        } else {
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
                $mc_id = (int) sql_insert_id();
            }

            goto_url(G5_PLUGIN_URL . '/maganda/admin/creators.php?id=' . $mc_id . '&saved=1');
        }
    }
}

if (!isset($edit_id)) {
    $edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}
if (!isset($edit) || !is_array($edit)) {
    $edit = $edit_id ? sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_id` = {$edit_id} ") : null;
}
if (isset($_GET['saved']) && $_GET['saved'] === '1') {
    $flash_message = '저장되었습니다. 홈페이지에서 방송이 정상 노출되는지 확인해 주세요.';
}

$list = array();
$result = sql_query(" SELECT * FROM `{$table}` ORDER BY `mc_sort` ASC, `mc_id` ASC ");
while ($row = sql_fetch_array($result)) {
    $list[] = $row;
}

$sample_row = sql_fetch(" SELECT * FROM `{$table}` WHERE `mc_slug` = '" . sql_escape_string(MAGANDA_SAMPLE_LIVE_SLUG) . "' ");
$sample_ready = $sample_row
    && maganda_stream_video_id($sample_row) === maganda_youtube_id_from_url(MAGANDA_SAMPLE_LIVE_URL)
    && (int) $sample_row['mc_is_live'] === 1
    && (int) $sample_row['mc_enabled'] === 1;

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
.mg-required::after { content: ' *'; color: #dc2626; font-weight: 700; }
.mg-field-error { display: block; margin-top: 6px; color: #dc2626; font-size: 12px; }
.mg-input-error { border-color: #dc2626 !important; background: #fff5f5 !important; }
.mg-guide-box { margin: 0 0 16px; padding: 14px 16px; border: 1px solid #dbeafe; border-radius: 10px; background: #eff6ff; color: #1e3a8a; font-size: 13px; line-height: 1.7; }
.mg-guide-box strong { display: block; margin-bottom: 6px; }
.mg-guide-box ul { margin: 0; padding-left: 18px; }
.mg-flash-ok { margin: 0 0 16px; padding: 12px 14px; border-radius: 8px; background: #ecfdf5; border: 1px solid #86efac; color: #166534; }
.mg-flash-error { margin: 0 0 16px; padding: 12px 14px; border-radius: 8px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.mg-sample-box { margin: 0 0 16px; padding: 14px 16px; border: 1px solid #e9d5ff; border-radius: 10px; background: #faf5ff; }
.mg-sample-box p { margin: 0 0 10px; font-size: 13px; line-height: 1.6; }
</style>

<div class="local_ov01 local_ov">
    <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/index.php">마간다TV</a> &gt; 방송회원
</div>

<?php if ($flash_message !== '') { ?>
<div class="mg-flash-ok"><?php echo get_text($flash_message); ?></div>
<?php } ?>

<?php if (!empty($form_errors)) { ?>
<div class="mg-flash-error">
    <strong>입력값을 확인해 주세요.</strong>
    <ul style="margin:8px 0 0;padding-left:18px;">
        <?php foreach ($form_errors as $msg) { ?>
            <li><?php echo get_text($msg); ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<div class="mg-sample-box">
    <p>
        <strong>샘플 방송 (Jolie / <?php echo get_text(MAGANDA_SAMPLE_LIVE_SLUG); ?>)</strong>
        <?php if ($sample_ready) { ?>
            — YouTube LIVE 연결 완료. <a href="<?php echo G5_URL; ?>/" target="_blank">홈페이지에서 보기</a>
        <?php } else { ?>
            — 아래 버튼을 누르면 샘플 URL이 자동 연결됩니다.<br>
            <code><?php echo get_text(MAGANDA_SAMPLE_LIVE_URL); ?></code>
        <?php } ?>
    </p>
    <form method="post" style="display:inline;">
        <?php echo get_admin_token(); ?>
        <input type="hidden" name="action" value="apply_sample">
        <input type="submit" value="샘플 YouTube LIVE 연결" class="btn_frmline" onclick="return confirm('Jolie(졸리) 방송회원에 샘플 YouTube LIVE URL을 적용합니다.\\n계속하시겠습니까?');">
    </form>
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
                <th>사용</th>
                <th>정렬</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($list)) { ?>
            <tr><td colspan="8">등록된 방송회원이 없습니다. 아래에서 추가하거나 샘플 LIVE를 연결해 주세요.</td></tr>
        <?php } else { foreach ($list as $row) {
            $yt_id = maganda_stream_video_id($row);
            $is_sample = $row['mc_slug'] === MAGANDA_SAMPLE_LIVE_SLUG;
        ?>
            <tr<?php echo $is_sample ? ' style="background:#faf5ff;"' : ''; ?>>
                <td><?php echo (int) $row['mc_id']; ?><?php echo $is_sample ? ' <span class="txt_small" style="color:#7c3aed;">샘플</span>' : ''; ?></td>
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
                <td><?php echo (int) $row['mc_enabled'] === 1 ? 'Y' : 'N'; ?></td>
                <td><?php echo (int) $row['mc_sort']; ?></td>
                <td><a href="?id=<?php echo (int) $row['mc_id']; ?>">수정</a></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>

<h2 class="h2_frm"><?php echo $edit ? '방송회원 수정' : '방송회원 추가'; ?></h2>

<div class="mg-guide-box">
    <strong>필수 입력 안내 (LIVE 방송 노출)</strong>
    <ul>
        <li><b>슬러그</b> — 영문 소문자 ID (예: jolie). URL/API 식별에 사용됩니다.</li>
        <li><b>이름</b> — 방송회원 표시 이름</li>
        <li><b>방송 제목</b> — 홈 화면 라이브 카드 제목</li>
        <li><b>YouTube 라이브 URL</b> — LIVE ON일 때 필수 (예: watch?v=Jav-pWT70rg)</li>
        <li><b>LIVE ON + 사용</b> — 두 옵션을 모두 켜야 홈 “지금 라이브 중”에 노출됩니다.</li>
    </ul>
</div>

<form method="post" id="mgCreatorForm" novalidate>
    <?php echo get_admin_token(); ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="mc_id" value="<?php echo $edit ? (int) $edit['mc_id'] : 0; ?>">
    <input type="hidden" name="mc_youtube_id" id="mc_youtube_id" value="<?php echo $edit ? get_text($edit['mc_youtube_id']) : ''; ?>">
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <tbody>
                <tr>
                    <th class="mg-required">슬러그</th>
                    <td>
                        <input type="text" name="mc_slug" id="mc_slug" value="<?php echo $edit ? get_text($edit['mc_slug']) : ''; ?>" class="frm_input <?php echo isset($form_errors['mc_slug']) ? 'mg-input-error' : ''; ?>" required pattern="[a-z0-9_-]+" placeholder="jolie">
                        <span class="mg-stream-help">영문 소문자, 숫자, -, _ 만 사용</span>
                        <?php if (isset($form_errors['mc_slug'])) { ?><span class="mg-field-error"><?php echo get_text($form_errors['mc_slug']); ?></span><?php } ?>
                    </td>
                </tr>
                <tr>
                    <th class="mg-required">이름</th>
                    <td>
                        <input type="text" name="mc_name" id="mc_name" value="<?php echo $edit ? get_text($edit['mc_name']) : ''; ?>" class="frm_input <?php echo isset($form_errors['mc_name']) ? 'mg-input-error' : ''; ?>" required placeholder="Jolie (졸리)">
                        <?php if (isset($form_errors['mc_name'])) { ?><span class="mg-field-error"><?php echo get_text($form_errors['mc_name']); ?></span><?php } ?>
                    </td>
                </tr>
                <tr>
                    <th class="mg-required">방송 제목</th>
                    <td>
                        <input type="text" name="mc_title" id="mc_title" value="<?php echo $edit ? get_text($edit['mc_title']) : ''; ?>" class="frm_input mg-stream-field <?php echo isset($form_errors['mc_title']) ? 'mg-input-error' : ''; ?>" required placeholder="보라카이 해변 실시간 LIVE">
                        <?php if (isset($form_errors['mc_title'])) { ?><span class="mg-field-error"><?php echo get_text($form_errors['mc_title']); ?></span><?php } ?>
                    </td>
                </tr>
                <tr><th>카테고리</th><td><input type="text" name="mc_category" value="<?php echo $edit ? get_text($edit['mc_category']) : ''; ?>" class="frm_input" placeholder="여행"></td></tr>
                <tr><th>소개</th><td><input type="text" name="mc_intro" value="<?php echo $edit ? get_text($edit['mc_intro']) : ''; ?>" class="frm_input mg-stream-field" placeholder="방송 한 줄 소개"></td></tr>
                <tr>
                    <th class="mg-required">YouTube 라이브 URL</th>
                    <td>
                        <input type="url" name="mc_stream_url" id="mc_stream_url" value="<?php echo $edit ? get_text($edit['mc_stream_url']) : ''; ?>" class="frm_input mg-stream-field <?php echo isset($form_errors['mc_stream_url']) ? 'mg-input-error' : ''; ?>" placeholder="<?php echo get_text(MAGANDA_SAMPLE_LIVE_URL); ?>">
                        <p class="mg-stream-help">
                            LIVE ON 상태에서는 <b>필수</b>입니다. URL 붙여넣기 → 영상 ID 자동 추출 → 미리보기 확인<br>
                            지원: <code>watch?v=</code>, <code>youtu.be/</code>, <code>/live/</code>, <code>/shorts/</code>
                        </p>
                        <?php if (isset($form_errors['mc_stream_url'])) { ?><span class="mg-field-error"><?php echo get_text($form_errors['mc_stream_url']); ?></span><?php } ?>
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
                <tr><th>시청자 수</th><td><input type="number" name="mc_viewers" value="<?php echo $edit ? (int) $edit['mc_viewers'] : 0; ?>" class="frm_input" min="0"></td></tr>
                <tr><th>정렬</th><td><input type="number" name="mc_sort" value="<?php echo $edit ? (int) $edit['mc_sort'] : 0; ?>" class="frm_input" min="0"><span class="mg-stream-help">숫자가 작을수록 먼저 노출</span></td></tr>
                <tr>
                    <th>옵션</th>
                    <td>
                        <label><input type="checkbox" name="mc_is_live" id="mc_is_live" value="1" <?php echo $edit && (int) $edit['mc_is_live'] === 1 ? 'checked' : ''; ?>> LIVE ON (홈 “지금 라이브 중” 노출)</label><br>
                        <label><input type="checkbox" name="mc_enabled" id="mc_enabled" value="1" <?php echo !$edit || (int) $edit['mc_enabled'] === 1 ? 'checked' : ''; ?>> 사용 (비활성 시 API/홈에서 숨김)</label>
                        <?php if (isset($form_errors['mc_enabled'])) { ?><span class="mg-field-error"><?php echo get_text($form_errors['mc_enabled']); ?></span><?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="저장" class="btn_submit">
        <a href="<?php echo G5_PLUGIN_URL; ?>/maganda/admin/creators.php" class="btn_frmline">새로 추가</a>
        <?php if ($edit && $preview_id !== '') { ?>
            <a href="<?php echo G5_URL; ?>/#mg-live" target="_blank" class="btn_frmline">홈페이지에서 보기</a>
        <?php } ?>
    </div>
</form>
<script>
(function () {
    var form = document.getElementById('mgCreatorForm');
    var urlInput = document.getElementById('mc_stream_url');
    var idInput = document.getElementById('mc_youtube_id');
    var idLabel = document.getElementById('mgStreamVideoId');
    var frame = document.getElementById('mgStreamPreviewFrame');
    var empty = document.getElementById('mgStreamPreviewEmpty');
    var liveInput = document.getElementById('mc_is_live');
    var enabledInput = document.getElementById('mc_enabled');
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

    function markError(el, on) {
        if (!el) return;
        el.classList.toggle('mg-input-error', !!on);
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
                empty.textContent = urlInput.value.trim()
                    ? 'YouTube URL 형식을 확인해 주세요. (watch?v=, youtu.be/ 등)'
                    : 'YouTube URL을 입력하면 미리보기가 표시됩니다.';
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
        if (!badge) return;
        if (liveInput.checked) {
            badge.textContent = 'LIVE ON';
            badge.className = 'mg-stream-badge mg-stream-badge--live';
        } else {
            badge.textContent = 'LIVE OFF';
            badge.className = 'mg-stream-badge mg-stream-badge--off';
        }
    }

    function validateForm() {
        var errors = [];
        var slug = document.getElementById('mc_slug');
        var name = document.getElementById('mc_name');
        var title = document.getElementById('mc_title');
        markError(slug, false);
        markError(name, false);
        markError(title, false);
        markError(urlInput, false);

        if (!slug.value.trim()) {
            errors.push('슬러그는 필수입니다.');
            markError(slug, true);
        } else if (!/^[a-z0-9_-]+$/.test(slug.value.trim())) {
            errors.push('슬러그는 영문 소문자, 숫자, -, _ 만 사용할 수 있습니다.');
            markError(slug, true);
        }
        if (!name.value.trim()) {
            errors.push('이름은 필수입니다.');
            markError(name, true);
        }
        if (!title.value.trim()) {
            errors.push('방송 제목은 필수입니다.');
            markError(title, true);
        }
        if (liveInput.checked && !enabledInput.checked) {
            errors.push('LIVE ON 상태에서는 "사용"도 함께 켜 주세요.');
        }
        syncPreview();
        var videoId = idInput.value.trim();
        if (liveInput.checked && !videoId) {
            errors.push('LIVE ON 상태에서는 YouTube 라이브 URL이 필수입니다.');
            markError(urlInput, true);
        } else if (urlInput.value.trim() && !videoId) {
            errors.push('YouTube URL 형식을 확인해 주세요.');
            markError(urlInput, true);
        }
        if (errors.length) {
            alert(errors.join('\\n'));
            return false;
        }
        return true;
    }

    urlInput.addEventListener('input', syncPreview);
    urlInput.addEventListener('change', syncPreview);
    if (liveInput) liveInput.addEventListener('change', syncBadge);
    form.addEventListener('submit', function (e) {
        syncPreview();
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    syncPreview();
})();
</script>
<?php include_once G5_ADMIN_PATH . '/admin.tail.php'; ?>
