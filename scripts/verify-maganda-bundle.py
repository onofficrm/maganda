#!/usr/bin/env python3
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
BUNDLE = ROOT / "plugin/onoff-builder-bridge/imports/maganda/assets/index-DUQOkQrF.js"

def main():
    path = Path(sys.argv[1]) if len(sys.argv) > 1 else BUNDLE
    data = path.read_text(encoding="utf-8")
    errors = []
    if "function mgT(" not in data:
        errors.append("missing mgT")
    if "function mgGo(" not in data:
        errors.append("missing mgGo")
    if "function mgYoutubeEmbedUrl(" not in data:
        errors.append("missing mgYoutubeEmbedUrl")
    if "function mgCreatorPayload(" not in data:
        errors.append("missing mgCreatorPayload")
    if "mgApi(" not in data:
        errors.append("missing mgApi")
    for name in ["}const Xp=", "}const Vp=", "const Vp=", "const Kp="]:
        i = data.find(name)
        chunk = data[i : i + 800] if i >= 0 else ""
        if "mgT(" in chunk:
            errors.append(f"mgT in module data near {name}")
    if errors:
        print("FAIL:", ", ".join(errors))
        sys.exit(1)
    print("OK:", path.name, "mgT=", data.count("mgT("))

if __name__ == "__main__":
    main()
