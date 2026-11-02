#!/usr/bin/env python3
"""Brand and style preset manager for seo-image-gen (~/.banana/presets/*.json)."""

import argparse
import json
import sys
from pathlib import Path


def get_presets_dir() -> Path:
    d = Path.home() / ".banana" / "presets"
    d.mkdir(parents=True, exist_ok=True)
    return d


def cmd_list(_args: argparse.Namespace) -> int:
    d = get_presets_dir()
    files = sorted(d.glob("*.json"))
    if not files:
        print("No presets configured yet in ~/.banana/presets/.")
        return 0
    for f in files:
        try:
            data = json.loads(f.read_text(encoding="utf-8"))
            desc = data.get("description", "")
            print(f"- {data.get('name', f.stem)}: {desc}")
        except Exception as exc:
            print(f"- {f.stem}: (invalid JSON: {exc})")
    return 0


def cmd_show(args: argparse.Namespace) -> int:
    target = get_presets_dir() / f"{args.name}.json"
    if not target.is_file():
        print(f"Preset '{args.name}' not found at {target}", file=sys.stderr)
        return 1
    print(target.read_text(encoding="utf-8"))
    return 0


def cmd_create(args: argparse.Namespace) -> int:
    colors = [c.strip() for c in (args.colors or "").split(",") if c.strip()]
    payload = {
        "name": args.name,
        "description": args.description or f"{args.name} preset",
        "colors": colors,
        "style": args.style or "",
        "typography": args.typography or "",
        "lighting": args.lighting or "",
        "mood": args.mood or "",
        "default_ratio": args.ratio or "16:9",
        "default_resolution": args.resolution or "1K",
    }
    target = get_presets_dir() / f"{args.name}.json"
    target.write_text(json.dumps(payload, indent=2) + "\n", encoding="utf-8")
    print(f"Saved preset '{args.name}' -> {target}")
    return 0


def cmd_delete(args: argparse.Namespace) -> int:
    if not args.confirm:
        print("Refusing to remove preset without --confirm.", file=sys.stderr)
        return 1
    target = get_presets_dir() / f"{args.name}.json"
    if not target.is_file():
        print(f"Preset '{args.name}' does not exist.", file=sys.stderr)
        return 1
    target.unlink()
    print(f"Removed preset '{args.name}'.")
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description="Manage seo-image-gen brand presets.")
    sub = parser.add_subparsers(dest="command", required=True)

    p_list = sub.add_parser("list", help="List available presets")
    p_list.set_defaults(func=cmd_list)

    p_show = sub.add_parser("show", help="Show preset JSON")
    p_show.add_argument("name")
    p_show.set_defaults(func=cmd_show)

    p_create = sub.add_parser("create", help="Create or update a preset")
    p_create.add_argument("name")
    p_create.add_argument("--description", default="")
    p_create.add_argument("--colors", default="")
    p_create.add_argument("--style", default="")
    p_create.add_argument("--typography", default="")
    p_create.add_argument("--lighting", default="")
    p_create.add_argument("--mood", default="")
    p_create.add_argument("--ratio", default="16:9")
    p_create.add_argument("--resolution", default="1K")
    p_create.set_defaults(func=cmd_create)

    p_del = sub.add_parser("delete", help="Delete a preset")
    p_del.add_argument("name")
    p_del.add_argument("--confirm", action="store_true")
    p_del.set_defaults(func=cmd_delete)

    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    sys.exit(main())
