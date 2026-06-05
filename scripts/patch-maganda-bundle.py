#!/usr/bin/env python3
"""Patch Maganda builder bundle: i18n + links + API integration (P0-P3)."""
import importlib.util
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
I18N = ROOT / "scripts" / "patch-maganda-i18n.py"

spec = importlib.util.spec_from_file_location("maganda_i18n", I18N)
mod = importlib.util.module_from_spec(spec)
spec.loader.exec_module(mod)

MG_BOOT = (
    'function mgCfg(){return window.__MAGANDA__||{urls:{},api:"/plugin/maganda/api/",member:{logged:!1,nick:"",points:0},sections:{},default_creator:null}}'
    'function mgUrl(k){const c=mgCfg();return c.urls&&c.urls[k]?c.urls[k]:"#"}'
    'function mgGo(k){const u=mgUrl(k);if(u&&u!=="#")location.href=u}'
    'function mgScroll(id){if(!id||id==="home"){window.scrollTo({top:0,behavior:"smooth"});return}const el=document.getElementById(id);if(el)el.scrollIntoView({behavior:"smooth"})}'
    'function mgApi(p){const c=mgCfg();return (c.api||"/plugin/maganda/api/")+p}'
    'function mgRequireLogin(){const c=mgCfg();if(c.member&&c.member.logged)return!0;mgGo("login");return!1}'
    'function mgYoutubeId(u){if(!u)return"";if(/^[a-zA-Z0-9_-]{11}$/.test(u))return u;const m=String(u).match(/(?:v=|youtu\\.be\\/|embed\\/)([a-zA-Z0-9_-]{11})/);return m?m[1]:""}'
    'function mgGiftKey(n){return ({하트:"heart",커피:"coffee",꽃다발:"flower",왕관:"crown"})[n]||"heart"}'
)

ROOT_P_OLD = (
    'function $p(){const[o,S]=Pl.useState("home"),[F,hl]=Pl.useState(mgDetectLang);'
    'Pl.useEffect(()=>{try{localStorage.setItem("maganda_lang",F)}catch(e){}},[F]);'
    'return c.jsx(Yp,{currentView:o,setCurrentView:S,lang:F,setLang:hl,'
    'children:o==="home"?c.jsx(Zp,{onNavigate:S,lang:F}):c.jsx(Jp,{onNavigate:S,lang:F})})}'
)
ROOT_P_NEW = (
    'function $p(){const[o,S]=Pl.useState("home"),[F,hl]=Pl.useState(mgDetectLang),[R,rl]=Pl.useState(null);'
    'Pl.useEffect(()=>{try{localStorage.setItem("maganda_lang",F)}catch(e){}},[F]);'
    'const nav=(v,C)=>{if(C)rl(C);S(v)};'
    'return c.jsx(Yp,{currentView:o,setCurrentView:S,lang:F,setLang:hl,'
    'children:o==="home"?c.jsx(Zp,{onNavigate:nav,lang:F}):c.jsx(Jp,{onNavigate:v=>{if(v==="home")rl(null);S(v)},lang:F,creator:R})})}'
)

ZP_HEAD_OLD = 'function Zp({onNavigate:o,lang:F}){const[S,v]=Pl.useState("daily");return c.jsxs("div",{className:"w-full",children:['
ZP_HEAD_NEW = (
    'function Zp({onNavigate:o,lang:F}){const[S,v]=Pl.useState("daily"),[liveData,setLiveData]=Pl.useState(Xp),'
    '[creatorData,setCreatorData]=Pl.useState(Qp),[rankData,setRankData]=Pl.useState({daily:Lp,weekly:Lp,monthly:Lp});'
    'Pl.useEffect(()=>{fetch(mgApi("home.php")).then(r=>r.json()).then(d=>{if(!d||!d.ok)return;'
    'if(d.live&&d.live.length)setLiveData(d.live);if(d.creators&&d.creators.length)setCreatorData(d.creators);'
    'if(d.ranking)setRankData(d.ranking)}).catch(()=>{})},[]);'
    'return c.jsxs("div",{className:"w-full",children:['
)

JP_HEAD_OLD = 'function Jp({onNavigate:o,lang:F}){const[S,v]=Pl.useState(Vp),[f,E]=Pl.useState(""),[M,D]=Pl.useState(!1),Q=Pl.useRef(null);'
JP_HEAD_NEW = (
    'function Jp({onNavigate:o,lang:F,creator:C}){const rc=C||mgCfg().default_creator,'
    '[mc,setMc]=Pl.useState(rc),[S,v]=Pl.useState(Vp),[f,E]=Pl.useState(""),[M,D]=Pl.useState(!1),'
    '[following,setFollowing]=Pl.useState(!1),[bal,setBal]=Pl.useState(((mgCfg().member||{}).points)||0),Q=Pl.useRef(null);'
    'Pl.useEffect(()=>{if(!rc)return;const q=rc.slug||rc.id;fetch(mgApi("creator.php?slug="+encodeURIComponent(q)))'
    '.then(r=>r.json()).then(d=>{if(d.ok&&d.creator)setMc(d.creator);if(d.ok&&d.chat)v(d.chat);if(d.ok)setFollowing(!!d.following)}).catch(()=>{})},[rc&&rc.id]);'
    'Pl.useEffect(()=>{if(!mc||!mc.id)return;const t=setInterval(()=>{fetch(mgApi("chat.php?creator_id="+mc.id))'
    '.then(r=>r.json()).then(d=>{if(d.ok&&d.messages)v(d.messages)}).catch(()=>{})},5e3);return()=>clearInterval(t)},[mc&&mc.id]);'
)

JP_SUBMIT_OLD = 'const _=w=>{w==null||w.preventDefault(),f.trim()&&(v(U=>[...U,{id:Date.now(),type:"chat",user:"나(Guest)",text:f,time:"10:05"}]),E(""))}'
JP_SUBMIT_NEW = (
    'const _=w=>{w==null||w.preventDefault();if(!f.trim()||!mc||!mc.id)return;'
    'const fd=new FormData();fd.append("creator_id",mc.id);fd.append("message",f);'
    'fetch(mgApi("chat-post.php"),{method:"POST",body:fd,credentials:"same-origin"})'
    '.then(r=>r.json()).then(d=>{if(d.ok){E("");fetch(mgApi("chat.php?creator_id="+mc.id)).then(r=>r.json()).then(x=>{if(x.ok&&x.messages)v(x.messages)})}}).catch(()=>{})}'
)

JP_GIFT_OLD = ',g=w=>{v(U=>[...U,{id:Date.now(),type:"gift",user:"나(Guest)",gift:w.name,points:w.points,text:"",time:"10:05",icon:w.icon,color:w.color}]),D(!1)}'
JP_GIFT_NEW = (
    ',g=w=>{if(!mgRequireLogin()||!mc||!mc.id)return;const fd=new FormData();'
    'fd.append("creator_id",mc.id);fd.append("gift_key",mgGiftKey(w.name));'
    'fetch(mgApi("gift.php"),{method:"POST",body:fd,credentials:"same-origin"})'
    '.then(r=>r.json()).then(d=>{if(d.ok){setBal(d.points);D(!1);'
    'fetch(mgApi("chat.php?creator_id="+mc.id)).then(r=>r.json()).then(x=>{if(x.ok&&x.messages)v(x.messages)})}'
    'else if(d.error==="insufficient_points")mgGo("point_charge");else alert(d.message||"")}).catch(()=>{})}'
)

YP_REPLACEMENTS = [
    ('c.jsx("span",{className:g,children:mgT("라이브",M)})',
     'c.jsx("button",{type:"button",className:g+" bg-transparent border-0",onClick:()=>mgScroll("mg-live"),children:mgT("라이브",M)})'),
    ('c.jsx("span",{className:g,children:mgT("인기방송",M)})',
     'c.jsx("button",{type:"button",className:g+" bg-transparent border-0",onClick:()=>mgScroll("mg-popular"),children:mgT("인기방송",M)})'),
    ('c.jsx("span",{className:g,children:mgT("방송회원",M)})',
     'c.jsx("button",{type:"button",className:g+" bg-transparent border-0",onClick:()=>mgScroll("mg-creators"),children:mgT("방송회원",M)})'),
    ('c.jsx("span",{className:g,children:mgT("랭킹",M)})',
     'c.jsx("button",{type:"button",className:g+" bg-transparent border-0",onClick:()=>mgScroll("mg-ranking"),children:mgT("랭킹",M)})'),
    ('c.jsx("span",{className:g,children:mgT("고객센터",M)})',
     'c.jsx("button",{type:"button",className:g+" bg-transparent border-0",onClick:()=>mgGo("support"),children:mgT("고객센터",M)})'),
    ('c.jsx("button",{className:"text-sm font-medium text-gray-600 hover:text-purple-600 transition-colors",children:mgT("로그인",M)})',
     'c.jsx("button",{type:"button",className:"text-sm font-medium text-gray-600 hover:text-purple-600 transition-colors",onClick:()=>mgGo("login"),children:mgT("로그인",M)})'),
    ('c.jsx("button",{className:"text-sm font-medium text-gray-600 hover:text-purple-600 transition-colors",children:mgT("회원가입",M)})',
     'c.jsx("button",{type:"button",className:"text-sm font-medium text-gray-600 hover:text-purple-600 transition-colors",onClick:()=>mgGo("register"),children:mgT("회원가입",M)})'),
    ('c.jsxs("button",{className:"px-4 py-2 bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors rounded-full text-sm font-bold flex items-center gap-1",children:[c.jsx(Gn,{className:"w-4 h-4"}),mgT("포인트 충전",M)]})',
     'c.jsxs("button",{type:"button",className:"px-4 py-2 bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors rounded-full text-sm font-bold flex items-center gap-1",onClick:()=>mgGo("point_charge"),children:[c.jsx(Gn,{className:"w-4 h-4"}),mgT("포인트 충전",M)]})'),
    ('c.jsx("button",{className:"px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 transition-all",children:mgT("방송회원 신청",M)})',
     'c.jsx("button",{type:"button",className:"px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 transition-all",onClick:()=>mgGo("apply"),children:mgT("방송회원 신청",M)})'),
    ('c.jsx("span",{className:"hover:text-white cursor-pointer transition-colors",children:mgT("공지사항",M)})',
     'c.jsx("button",{type:"button",className:"hover:text-white cursor-pointer transition-colors bg-transparent border-0",onClick:()=>mgGo("notice"),children:mgT("공지사항",M)})'),
    ('c.jsx("span",{className:"hover:text-white cursor-pointer transition-colors",children:mgT("이용약관",M)})',
     'c.jsx("button",{type:"button",className:"hover:text-white cursor-pointer transition-colors bg-transparent border-0",onClick:()=>mgGo("terms"),children:mgT("이용약관",M)})'),
    ('c.jsx("span",{className:"hover:text-white cursor-pointer transition-colors",children:mgT("개인정보처리방침",M)})',
     'c.jsx("button",{type:"button",className:"hover:text-white cursor-pointer transition-colors bg-transparent border-0",onClick:()=>mgGo("privacy"),children:mgT("개인정보처리방침",M)})'),
    ('c.jsx("span",{className:"hover:text-white cursor-pointer transition-colors",children:mgT("환불정책",M)})',
     'c.jsx("button",{type:"button",className:"hover:text-white cursor-pointer transition-colors bg-transparent border-0",onClick:()=>mgGo("refund"),children:mgT("환불정책",M)})'),
    ('c.jsxs("div",{className:"flex flex-col items-center gap-1 text-purple-600 cursor-pointer",children:[c.jsx(p1,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("홈",M)})]}',
     'c.jsxs("button",{type:"button",className:"flex flex-col items-center gap-1 text-purple-600 cursor-pointer bg-transparent border-0",onClick:()=>{v("home");mgScroll("home")},children:[c.jsx(p1,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("홈",M)})]}'),
    ('c.jsxs("div",{className:"flex flex-col items-center gap-1 text-gray-400 cursor-pointer",children:[c.jsx(Yn,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("라이브",M)})]}',
     'c.jsxs("button",{type:"button",className:"flex flex-col items-center gap-1 text-gray-400 cursor-pointer bg-transparent border-0",onClick:()=>{v("home");mgScroll("mg-live")},children:[c.jsx(Yn,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("라이브",M)})]}'),
    ('c.jsxs("div",{className:"flex flex-col items-center gap-1 text-gray-400 cursor-pointer",children:[c.jsx(E1,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("마이",M)})]}',
     'c.jsxs("button",{type:"button",className:"flex flex-col items-center gap-1 text-gray-400 cursor-pointer bg-transparent border-0",onClick:()=>mgGo("my"),children:[c.jsx(E1,{className:"w-5 h-5"}),c.jsx("span",{className:"text-[10px] font-bold",children:mgT("마이",M)})]}'),
]


def inject_mg_boot(data):
    marker = 'const Ds=[{code:"ko",label:"한국어"}'
    if "function mgGo(" in data:
        return data
    if "function mgT(" not in data:
        data = data.replace(marker, mod.I18N_BOOT + marker, 1)
    return data.replace(marker, MG_BOOT + marker, 1)


def patch_navigation(data):
    yp_s, yp_e = mod.function_spans(data)["Yp"]
    yp = data[yp_s:yp_e]
    for old, new in YP_REPLACEMENTS:
        if old in yp:
            yp = yp.replace(old, new, 1)
    return data[:yp_s] + yp + data[yp_e:]


def patch_root_and_pages(data):
    if ROOT_P_OLD in data:
        data = data.replace(ROOT_P_OLD, ROOT_P_NEW, 1)
    if ZP_HEAD_OLD in data:
        data = data.replace(ZP_HEAD_OLD, ZP_HEAD_NEW, 1)
    if JP_HEAD_OLD in data:
        data = data.replace(JP_HEAD_OLD, JP_HEAD_NEW, 1)
    if JP_SUBMIT_OLD in data:
        data = data.replace(JP_SUBMIT_OLD, JP_SUBMIT_NEW, 1)
    if JP_GIFT_OLD in data:
        data = data.replace(JP_GIFT_OLD, JP_GIFT_NEW, 1)

    zp_s, zp_e = mod.function_spans(data)["Zp"]
    zp = data[zp_s:zp_e]
    zp = zp.replace("Xp.map", "liveData.map", 1)
    zp = zp.replace("Qp.map", "creatorData.map", 1)
    zp = zp.replace("Lp.map", "(rankData[S]||Lp).map", 1)
    zp = zp.replace('onClick:()=>o("room"),className:"px-8', 'onClick:()=>o("room",liveData[0]||null),className:"px-8', 1)
    zp = zp.replace('onClick:()=>o("room"),children:[c.jsxs("div",{className:"relat', 'onClick:()=>o("room",f),children:[c.jsxs("div",{className:"relat', 1)
    zp = zp.replace(
        'c.jsx("section",{className:"py-20 bg-slate-50 px-4",children:c.jsxs("div",{className:"max-w-7xl mx-auto flex flex-col gap-10",children:[c.jsxs("div",{className:"flex flex-col md:flex-row justify-between items-end gap-4",children:[c.jsxs("div",{children:[c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("지금 라이브 중",F)',
        'c.jsx("section",{id:"mg-live",className:"py-20 bg-slate-50 px-4",children:c.jsxs("div",{className:"max-w-7xl mx-auto flex flex-col gap-10",children:[c.jsxs("div",{className:"flex flex-col md:flex-row justify-between items-end gap-4",children:[c.jsxs("div",{children:[c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("지금 라이브 중",F)',
        1,
    )
    zp = zp.replace(
        'c.jsx("section",{className:"py-20 bg-white px-4",children:c.jsxs("div",{className:"max-w-7xl mx-auto flex flex-col gap-10",children:[c.jsxs("div",{className:"flex flex-col items-center text-center",children:[c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("인기 방송회원",F)',
        'c.jsx("section",{id:"mg-popular",className:"py-20 bg-white px-4",children:c.jsxs("div",{className:"max-w-7xl mx-auto flex flex-col gap-10",children:[c.jsxs("div",{className:"flex flex-col items-center text-center",children:[c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("인기 방송회원",F)',
        1,
    )
    zp = zp.replace(
        'c.jsx("section",{className:"py-20 bg-slate-50 px-4",children:c.jsxs("div",{className:"max-w-3xl mx-auto flex flex-col gap-8",children:[c.jsxs("div",{className:"text-center flex flex-col items-center",children:[c.jsx("div",{className:"w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4",children:c.jsx(m0,{className:"text-yellow-600 w-6 h-6"})}),c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("오늘의 후원 랭킹",F)',
        'c.jsx("section",{id:"mg-ranking",className:"py-20 bg-slate-50 px-4",children:c.jsxs("div",{className:"max-w-3xl mx-auto flex flex-col gap-8",children:[c.jsxs("div",{className:"text-center flex flex-col items-center",children:[c.jsx("div",{className:"w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4",children:c.jsx(m0,{className:"text-yellow-600 w-6 h-6"})}),c.jsx("h2",{className:"text-3xl font-bold text-gray-900 tracking-tight",children:mgT("오늘의 후원 랭킹",F)',
        1,
    )
    zp = zp.replace(
        'c.jsxs("section",{className:"py-24 relative overflow-hidden bg-slate-900 px-4",children:',
        'c.jsxs("section",{id:"mg-creators",className:"py-24 relative overflow-hidden bg-slate-900 px-4",children:',
        1,
    )
    zp = zp.replace(
        'c.jsx("button",{className:"px-6 py-3 bg-white text-purple-900 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-purple-50 transition-all flex items-center gap-2 w-max",children:mgT("방송회원 신청하기",F)})',
        'c.jsx("button",{type:"button",className:"px-6 py-3 bg-white text-purple-900 rounded-xl font-bold shadow-lg hover:shadow-xl hover:bg-purple-50 transition-all flex items-center gap-2 w-max",onClick:()=>mgGo("apply"),children:mgT("방송회원 신청하기",F)})',
        1,
    )
    data = data[:zp_s] + zp + data[zp_e:]

    jp_s, jp_e = mod.function_spans(data)["Jp"]
    jp = data[jp_s:jp_e]
    jp = jp.replace('mgT("Jolie (졸리)",F)', '(mc&&mc.name?mc.name:mgT("Jolie (졸리)",F))', 1)
    jp = jp.replace('mgT("보라카이 해변 실시간 소통",F)', '(mc&&mc.subtitle?mc.subtitle:mgT("보라카이 해변 실시간 소통",F))', 1)
    jp = jp.replace('mgT("보유 0P",F)', '(bal.toLocaleString()+"P")', 1)
    jp = jp.replace(
        'c.jsx("button",{className:"ml-2 px-3 py-1 bg-pink-500 hover:bg-pink-600 rounded-full text-xs font-bold transition-colors",children:mgT("팔로우",F)})',
        'c.jsx("button",{type:"button",className:"ml-2 px-3 py-1 bg-pink-500 hover:bg-pink-600 rounded-full text-xs font-bold transition-colors",onClick:()=>{if(!mgRequireLogin()||!mc||!mc.id)return;const fd=new FormData();fd.append("creator_id",mc.id);fetch(mgApi("follow.php"),{method:"POST",body:fd,credentials:"same-origin"}).then(r=>r.json()).then(d=>{if(d.ok)setFollowing(d.following)}).catch(()=>{})},children:mgT("팔로우",F)})',
        1,
    )
    jp = jp.replace(
        'c.jsx("button",{className:"text-[10px] font-bold bg-pink-50 text-pink-600 px-2 py-1 rounded",children:mgT("충전",F)})',
        'c.jsx("button",{type:"button",className:"text-[10px] font-bold bg-pink-50 text-pink-600 px-2 py-1 rounded",onClick:()=>mgGo("point_charge"),children:mgT("충전",F)})',
        1,
    )
    if "youtube.com/embed" not in jp:
        jp = jp.replace(
            'c.jsx("img",{src:"https://images.unsplash.com/photo-1540202404-b71114227fe1?w=1600&q=80",className:"absolute inset-0 w-full h-full object-cover opacity-60",alt:"broadcast"})',
            'mc&&mgYoutubeId(mc.youtube_id||mc.stream_url)?c.jsx("iframe",{src:"https://www.youtube.com/embed/"+mgYoutubeId(mc.youtube_id||mc.stream_url)+"?autoplay=1&mute=1",className:"absolute inset-0 w-full h-full",allow:"autoplay; encrypted-media",allowFullScreen:!0}):c.jsx("img",{src:(mc&&mc.cover)||"https://images.unsplash.com/photo-1540202404-b71114227fe1?w=1600&q=80",className:"absolute inset-0 w-full h-full object-cover opacity-60",alt:"broadcast"})',
            1,
        )
    return data[:jp_s] + jp + data[jp_e:]


def patch(data):
    data = mod.patch(data)
    data = inject_mg_boot(data)
    data = patch_navigation(data)
    data = patch_root_and_pages(data)
    return data


def main():
    src = Path(sys.argv[1]) if len(sys.argv) > 1 else ROOT / "plugin/onoff-builder-bridge/imports/maganda/assets/index-DUQOkQrF.js"
    out = Path(sys.argv[2]) if len(sys.argv) > 2 else src
    data = src.read_text(encoding="utf-8")
    patched = patch(data)
    out.write_text(patched, encoding="utf-8")
    print("patched", out, "mgT=", patched.count("mgT("), "mgGo=", patched.count("mgGo("))


if __name__ == "__main__":
    main()
