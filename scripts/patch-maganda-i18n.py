#!/usr/bin/env python3
"""Patch Maganda builder JS bundle with i18n (function bodies only)."""
import json
import re
import sys
from pathlib import Path

TR = {
    "라이브": {"en": "Live", "ja": "ライブ", "zh": "直播"},
    "인기방송": {"en": "Popular", "ja": "人気配信", "zh": "热门直播"},
    "방송회원": {"en": "Creators", "ja": "配信者", "zh": "主播"},
    "랭킹": {"en": "Ranking", "ja": "ランキング", "zh": "排行榜"},
    "고객센터": {"en": "Support", "ja": "サポート", "zh": "客服中心"},
    "로그인": {"en": "Log in", "ja": "ログイン", "zh": "登录"},
    "회원가입": {"en": "Sign up", "ja": "会員登録", "zh": "注册"},
    "포인트 충전": {"en": "Top up points", "ja": "ポイントチャージ", "zh": "积分充值"},
    "방송회원 신청": {"en": "Apply as creator", "ja": "配信者申請", "zh": "申请成为主播"},
    "마간다TV": {"en": "Maganda TV", "ja": "Maganda TV", "zh": "Maganda TV"},
    "필리핀 라이브 전문 플랫폼": {"en": "Philippines live streaming platform", "ja": "フィリピンライブ専門プラットフォーム", "zh": "菲律宾直播专业平台"},
    "매력적인 필리핀 크리에이터와": {"en": "Connect live with talented", "ja": "魅力的なフィリピンクリエイターと", "zh": "与富有魅力的菲律宾创作者"},
    "라이브로 소통하세요": {"en": "Filipino creators", "ja": "ライブで交流しましょう", "zh": "直播互动"},
    "필리핀 현지 크리에이터들의 생생한 라이브 방송을 즐기고, 좋아하는 크리에이터에게 선물과 메시지로 응원할 수 있습니다.": {
        "en": "Enjoy live broadcasts from local Filipino creators and support your favorites with gifts and messages.",
        "ja": "フィリピンのクリエイターの生ライブを楽しみ、好きな配信者をギフトとメッセージで応援できます。",
        "zh": "欣赏菲律宾本地创作者的精彩直播，并用礼物和消息支持您喜爱的主播。",
    },
    " 지금 라이브 보기": {"en": " Watch live now", "ja": " 今すぐライブを見る", "zh": " 立即观看直播"},
    "방송회원 신청하기": {"en": "Apply as creator", "ja": "配信者に申請", "zh": "申请成为主播"},
    "지금 라이브 중": {"en": "Live now", "ja": "ライブ中", "zh": "正在直播"},
    "실시간으로 진행 중인 방송을 확인해보세요.": {"en": "Browse broadcasts happening right now.", "ja": "現在進行中の配信をチェックしましょう。", "zh": "查看正在进行中的直播。"},
    "인기 방송회원": {"en": "Popular creators", "ja": "人気配信者", "zh": "热门主播"},
    "많은 팬들이 응원하는 방송회원을 만나보세요.": {"en": "Meet creators loved by fans worldwide.", "ja": "多くのファンに愛される配信者に会いましょう。", "zh": "认识深受粉丝喜爱的主播。"},
    "오늘의 후원 랭킹": {"en": "Today's support ranking", "ja": "本日の支援ランキング", "zh": "今日打赏排行"},
    "랭킹 현황": {"en": "Ranking overview", "ja": "ランキング状況", "zh": "排名概况"},
    "일간 랭킹": {"en": "Daily", "ja": "日間", "zh": "日榜"},
    "주간 랭킹": {"en": "Weekly", "ja": "週間", "zh": "周榜"},
    "월간 랭킹": {"en": "Monthly", "ja": "月間", "zh": "月榜"},
    "방송회원 모집": {"en": "Creator recruitment", "ja": "配信者募集", "zh": "主播招募"},
    "유튜브 라이브 방송을 운영하고 있다면, 내 방송방을 만들고 팬들에게 안전하게 후원을 받아보세요.": {
        "en": "If you run YouTube live streams, create your room and receive support safely from fans.",
        "ja": "YouTubeライブを運営しているなら、自分の配信ルームを作り、ファンから安全に支援を受けましょう。",
        "zh": "如果您运营 YouTube 直播，创建您的直播间并安全接收粉丝打赏。",
    },
    "이용 방법은 간단합니다": {"en": "Getting started is easy", "ja": "使い方は簡単です", "zh": "使用方法很简单"},
    "가장 특별한 방법": {"en": "in the most special way", "ja": "最も特別な方法", "zh": "最特别的方式"},
    "시청 중인 크리에이터에게 특별한 선물로 마음을 표현해보세요. 화려한 이펙트와 함께 당신의 진심이 실시간으로 상단에 노출됩니다.": {
        "en": "Express your support with special gifts. Your message appears at the top with dazzling effects.",
        "ja": "特別なギフトで気持ちを伝えましょう。華やかなエフェクトとともにメッセージが上部に表示されます。",
        "zh": "用特别礼物表达心意，您的消息将带着华丽特效实时显示在顶部。",
    },
    "매력적인 필리핀 크리에이터들의 실시간 라이브를 시청하고, 포인트를 선물하며 가깝게 소통할 수 있는 프리미엄 후원 플랫폼입니다.": {
        "en": "A premium support platform to watch Filipino creators live, send gifts, and connect closely.",
        "ja": "フィリピンクリエイターのライブ視聴、ギフト、近い交流ができるプレミアム支援プラットフォームです。",
        "zh": "观看菲律宾创作者直播、赠送礼物并近距离互动的优质打赏平台。",
    },
    "안전한 정산 및 모니터링 시스템 적용 중": {"en": "Secure settlement and monitoring active", "ja": "安全な精算・監視システム適用中", "zh": "安全结算与监控系统运行中"},
    "서비스": {"en": "Services", "ja": "サービス", "zh": "服务"},
    "실시간 라이브": {"en": "Live streaming", "ja": "ライブ配信", "zh": "实时直播"},
    "방송회원 안내": {"en": "Creator guide", "ja": "配信者案内", "zh": "主播指南"},
    "공지사항": {"en": "Notices", "ja": "お知らせ", "zh": "公告"},
    "이용약관": {"en": "Terms of service", "ja": "利用規約", "zh": "服务条款"},
    "개인정보처리방침": {"en": "Privacy policy", "ja": "プライバシーポリシー", "zh": "隐私政策"},
    "환불정책": {"en": "Refund policy", "ja": "返金ポリシー", "zh": "退款政策"},
    "© 2026 마간다TV Corp. All rights reserved.": {"en": "© 2026 Maganda TV Corp. All rights reserved.", "ja": "© 2026 Maganda TV Corp. All rights reserved.", "zh": "© 2026 Maganda TV Corp. All rights reserved."},
    "마간다TV는 건전한 방송 환경과 투명한 정산을 위해 24시간 철저한 모니터링 시스템을 운영합니다.": {
        "en": "Maganda TV operates 24/7 monitoring for a healthy broadcast environment and transparent settlements.",
        "ja": "Maganda TVは健全な配信環境と透明な精算のため24時間監視システムを運用しています。",
        "zh": "Maganda TV 全天候监控，营造健康直播环境与透明结算。",
    },
    "홈": {"en": "Home", "ja": "ホーム", "zh": "首页"},
    "마이": {"en": "My", "ja": "マイ", "zh": "我的"},
    "실시간": {"en": "Live", "ja": "ライブ", "zh": "实时"},
    "실시간 채팅": {"en": "Live chat", "ja": "ライブチャット", "zh": "实时聊天"},
    "선물 선택": {"en": "Choose a gift", "ja": "ギフト選択", "zh": "选择礼物"},
    "충전": {"en": "Top up", "ja": "チャージ", "zh": "充值"},
    "보유 0P": {"en": "Balance 0P", "ja": "保有 0P", "zh": "余额 0P"},
    "누적 포인트": {"en": "Total points", "ja": "累計ポイント", "zh": "累计积分"},
    "팔로우": {"en": "Follow", "ja": "フォロー", "zh": "关注"},
    "팔로워": {"en": "Followers", "ja": "フォロワー", "zh": "粉丝"},
    "프로필 보기": {"en": "View profile", "ja": "プロフィールを見る", "zh": "查看资料"},
    "보라카이 해변 실시간 소통": {"en": "Live from Boracay beach", "ja": "ボラカイビーチからライブ", "zh": "长滩岛海滩实时互动"},
    "마간다TV 채팅 규정을 준수해주세요. 욕설, 비방, 광고 등의 메시지는 통보 없이 삭제될 수 있으며 제재를 받을 수 있습니다.": {
        "en": "Please follow Maganda TV chat rules. Abusive or promotional messages may be removed without notice.",
        "ja": "Maganda TVのチャット規則を守ってください。不適切なメッセージは削除される場合があります。",
        "zh": "请遵守 Maganda TV 聊天规则，辱骂或广告消息可能被删除并受到处罚。",
    },
    "전체보기 ": {"en": "View all ", "ja": "すべて見る ", "zh": "查看全部 "},
    "마음을 전하는 ": {"en": "Send your heart ", "ja": "気持ちを伝える ", "zh": "传递心意 "},
    " 포인트 충전하기": {"en": " Top up points", "ja": " ポイントをチャージ", "zh": " 充值积分"},
    "나도 방송회원이": {"en": "You can become", "ja": "あなたも配信者に", "zh": "您也可以成为"},
    "될 수 있습니다": {"en": "a creator too", "ja": "なれます", "zh": "主播"},
    "안전한 라이브": {"en": "Safe live", "ja": "安全なライブ", "zh": "安全直播"},
    "후원 플랫폼": {"en": "support platform", "ja": "支援プラットフォーム", "zh": "打赏平台"},
    "1단계": {"en": "Step 1", "ja": "STEP 1", "zh": "第1步"},
    "2단계": {"en": "Step 2", "ja": "STEP 2", "zh": "第2步"},
    "3단계": {"en": "Step 3", "ja": "STEP 3", "zh": "第3步"},
    "4단계": {"en": "Step 4", "ja": "STEP 4", "zh": "第4步"},
    "간편하게 가입하고 로그인": {"en": "Sign up and log in easily", "ja": "簡単に登録してログイン", "zh": "轻松注册并登录"},
    "선물하기 위한 포인트 준비": {"en": "Prepare points for gifting", "ja": "ギフト用ポイントを準備", "zh": "准备打赏积分"},
    "원하는 방송방에 입장": {"en": "Enter your favorite room", "ja": "好きな配信ルームに入室", "zh": "进入喜欢的直播间"},
    "채팅과 선물로 소통": {"en": "Chat and send gifts", "ja": "チャットとギフトで交流", "zh": "通过聊天和礼物互动"},
    "후원: ": {"en": "Support: ", "ja": "支援: ", "zh": "打赏: "},
    " 유튜브 라이브 URL 등록": {"en": " Register YouTube live URL", "ja": " YouTubeライブURL登録", "zh": " 注册 YouTube 直播 URL"},
    " 커스텀 내 방송방 생성": {"en": " Create your custom room", "ja": " カスタム配信ルーム作成", "zh": " 创建自定义直播间"},
    " 포인트 선물 받기 및 정산": {"en": " Receive gifts and settlements", "ja": " ギフト受取と精算", "zh": " 接收礼物与结算"},
    "투명한 충전/전송 내역": {"en": "Transparent top-up history", "ja": "透明なチャージ/送信履歴", "zh": "透明充值/转账记录"},
    "실시간 악성 유저 차단": {"en": "Real-time toxic user blocking", "ja": "リアルタイム悪質ユーザー遮断", "zh": "实时屏蔽恶意用户"},
    "24시간 관리자 모니터링": {"en": "24/7 admin monitoring", "ja": "24時間管理者監視", "zh": "24小时管理员监控"},
    "안전한 정산 승인 시스템": {"en": "Secure settlement approval", "ja": "安全な精算承認システム", "zh": "安全结算审批系统"},
    "라이브 시청": {"en": "Watch live", "ja": "ライブ視聴", "zh": "观看直播"},
    "선물 응원": {"en": "Gift support", "ja": "ギフト応援", "zh": "礼物打赏"},
    "Jolie (졸리)": {"en": "Jolie", "ja": "Jolie", "zh": "Jolie"},
}

I18N_BOOT = (
    "const MgI18n="
    + json.dumps(TR, ensure_ascii=False, separators=(",", ":"))
    + ";"
    + 'function mgDetectLang(){try{const s=localStorage.getItem("maganda_lang");if(s&&MgI18nLangs.includes(s))return s}catch(e){}'
    + 'const n=(navigator.language||"ko").toLowerCase();if(n.startsWith("en"))return"en";if(n.startsWith("ja"))return"ja";if(n.startsWith("zh"))return"zh";return"ko"}'
    + 'const MgI18nLangs=["ko","en","ja","zh"];'
    + 'function mgT(t,lang){if(!t||lang==="ko")return t;const row=MgI18n[t];return row&&(row[lang]||row.en)||t}'
)

def function_spans(data):
    """Return (start, end) for component bodies only — exclude module-level mock data."""
    spans = {
        "Yp": (data.find("function Yp"), data.find("}const Xp=")),
        "Zp": (data.find("function Zp"), data.find("}const Vp=")),
        "Jp": (data.find("function Jp"), data.find("function $p")),
    }
    for name, (start, end) in spans.items():
        if start < 0 or end < 0 or end <= start:
            raise SystemExit(f"could not locate {name} span")
    return spans


def apply_strings(chunk, lang_var):
    for ko in sorted(TR.keys(), key=len, reverse=True):
        esc = ko.replace("\\", "\\\\").replace('"', '\\"')
        if f'mgT("{esc}",' in chunk:
            continue
        chunk = chunk.replace(f'"{esc}"', f'mgT("{esc}",{lang_var})')
    return chunk


def patch(data):
    marker = 'const Ds=[{code:"ko",label:"한국어"}'
    if marker not in data:
        raise SystemExit("marker not found")
    if "function mgT(" not in data:
        data = data.replace(marker, I18N_BOOT + marker, 1)

    old_p = 'function $p(){const[o,S]=Pl.useState("home");return c.jsx(Yp,{currentView:o,setCurrentView:S,children:o==="home"?c.jsx(Zp,{onNavigate:S}):c.jsx(Jp,{onNavigate:S})})}'
    new_p = (
        'function $p(){const[o,S]=Pl.useState("home"),[F,hl]=Pl.useState(mgDetectLang);'
        "Pl.useEffect(()=>{try{localStorage.setItem(\"maganda_lang\",F)}catch(e){}},[F]);"
        "return c.jsx(Yp,{currentView:o,setCurrentView:S,lang:F,setLang:hl,"
        'children:o==="home"?c.jsx(Zp,{onNavigate:S,lang:F}):c.jsx(Jp,{onNavigate:S,lang:F})})}'
    )
    data = data.replace(old_p, new_p, 1)

    old_yp = 'function Yp({children:o,currentView:S,setCurrentView:v}){var w;const[f,E]=Pl.useState(!1),[M,D]=Pl.useState("ko"),[Q,_]=Pl.useState(!1)'
    new_yp = 'function Yp({children:o,currentView:S,setCurrentView:v,lang:M,setLang:D}){var w;const[f,E]=Pl.useState(!1),[Q,_]=Pl.useState(!1)'
    data = data.replace(old_yp, new_yp, 1)

    data = data.replace('function Zp({onNavigate:o})', 'function Zp({onNavigate:o,lang:F})', 1)
    data = data.replace('function Jp({onNavigate:o})', 'function Jp({onNavigate:o,lang:F})', 1)
    data = data.replace(
        "onClick:()=>{D(U.code),_(!1)}",
        'onClick:()=>{D(U.code),_(!1);try{localStorage.setItem("maganda_lang",U.code)}catch(e){}}',
        1,
    )

    for name, lang in [("Yp", "M"), ("Zp", "F"), ("Jp", "F")]:
        start, end = function_spans(data)[name]
        chunk = apply_strings(data[start:end], lang)
        data = data[:start] + chunk + data[end:]

    return data


def main():
    src = Path(sys.argv[1]) if len(sys.argv) > 1 else Path(__file__).resolve().parents[1] / "plugin/onoff-builder-bridge/imports/maganda/assets/index-DUQOkQrF.js"
    out = Path(sys.argv[2]) if len(sys.argv) > 2 else src
    data = src.read_text(encoding="utf-8")
    if "function mgT(" in data and "mgT(\"하트\",F)" in data:
        # broken build: restore from sibling backup or require original input
        pass
    patched = patch(data)
    out.write_text(patched, encoding="utf-8")
    print("patched", out, "mgT=", patched.count("mgT("))


if __name__ == "__main__":
    main()
