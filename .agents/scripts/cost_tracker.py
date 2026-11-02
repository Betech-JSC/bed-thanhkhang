#!/usr/bin/env python3
"""Cost tracking ledger for seo-image-gen (~/.banana/costs.json)."""

import argparse
import json
import sys
from datetime import datetime, timedelta, timezone
from pathlib import Path

PRICING_USD = {
    "512": 0.020,
    "1K": 0.039,
    "2K": 0.078,
    "4K": 0.156,
}


def ledger_path() -> Path:
    d = Path.home() / ".banana"
    d.mkdir(parents=True, exist_ok=True)
    return d / "costs.json"


def load_entries() -> list:
    p = ledger_path()
    if not p.is_file():
        return []
    try:
        data = json.loads(p.read_text(encoding="utf-8"))
        return data if isinstance(data, list) else []
    except Exception:
        return []


def save_entries(entries: list) -> None:
    ledger_path().write_text(json.dumps(entries, indent=2) + "\n", encoding="utf-8")


def unit_cost(resolution: str) -> float:
    return PRICING_USD.get(resolution.upper(), PRICING_USD["1K"])


def cmd_log(args: argparse.Namespace) -> int:
    entries = load_entries()
    cost = unit_cost(args.resolution)
    entry = {
        "timestamp": datetime.now(timezone.utc).isoformat(),
        "model": args.model,
        "resolution": args.resolution.upper(),
        "cost_usd": cost,
        "prompt": args.prompt,
    }
    entries.append(entry)
    save_entries(entries)
    print(f"Logged generation: {args.model} @ {entry['resolution']} (${cost:.3f})")
    return 0


def cmd_summary(_args: argparse.Namespace) -> int:
    entries = load_entries()
    total = sum(float(e.get("cost_usd", 0.0)) for e in entries)
    cutoff = datetime.now(timezone.utc) - timedelta(days=7)
    last_7d = 0.0
    for e in entries:
        try:
            ts = datetime.fromisoformat(e["timestamp"])
            if ts >= cutoff:
                last_7d += float(e.get("cost_usd", 0.0))
        except Exception:
            pass
    print(f"Total generations: {len(entries)}")
    print(f"Last 7 days cost : ${last_7d:.3f}")
    print(f"All-time cost    : ${total:.3f}")
    return 0


def cmd_today(_args: argparse.Namespace) -> int:
    today_prefix = datetime.now(timezone.utc).strftime("%Y-%m-%d")
    entries = [e for e in load_entries() if str(e.get("timestamp", "")).startswith(today_prefix)]
    total = sum(float(e.get("cost_usd", 0.0)) for e in entries)
    print(f"Today ({today_prefix} UTC): {len(entries)} image(s), ${total:.3f}")
    return 0


def cmd_estimate(args: argparse.Namespace) -> int:
    cost = unit_cost(args.resolution) * args.count
    print(f"Estimated cost for {args.count}x {args.model} @ {args.resolution.upper()}: ${cost:.3f}")
    return 0


def cmd_reset(args: argparse.Namespace) -> int:
    if not args.confirm:
        print("Refusing to reset ledger without --confirm.", file=sys.stderr)
        return 1
    save_entries([])
    print("Cost ledger reset.")
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description="Track seo-image-gen generation costs.")
    sub = parser.add_subparsers(dest="command", required=True)

    p_log = sub.add_parser("log")
    p_log.add_argument("--model", required=True)
    p_log.add_argument("--resolution", default="1K")
    p_log.add_argument("--prompt", default="")
    p_log.set_defaults(func=cmd_log)

    p_sum = sub.add_parser("summary")
    p_sum.set_defaults(func=cmd_summary)

    p_today = sub.add_parser("today")
    p_today.set_defaults(func=cmd_today)

    p_est = sub.add_parser("estimate")
    p_est.add_argument("--model", default="gemini-3.1-flash-image-preview")
    p_est.add_argument("--resolution", default="1K")
    p_est.add_argument("--count", type=int, default=1)
    p_est.set_defaults(func=cmd_estimate)

    p_rst = sub.add_parser("reset")
    p_rst.add_argument("--confirm", action="store_true")
    p_rst.set_defaults(func=cmd_reset)

    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    sys.exit(main())
