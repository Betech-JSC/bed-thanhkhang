#!/usr/bin/env python3
"""Fallback CLI wrapper for Gemini image generation when nanobanana MCP is unavailable."""

import argparse
import base64
import json
import os
import sys
import urllib.error
import urllib.request
from pathlib import Path


def main() -> int:
    parser = argparse.ArgumentParser(description="Generate an SEO asset image via Gemini REST API.")
    parser.add_argument("--prompt", required=True, help="Detailed visual prompt")
    parser.add_argument("--aspect-ratio", default="16:9", help="Target aspect ratio (e.g. 16:9, 4:3, 1:1)")
    parser.add_argument("--model", default="gemini-2.5-flash-image", help="Gemini image model name")
    parser.add_argument("--output", default="seo-generated-image.png", help="Output image file path")
    args = parser.parse_args()

    api_key = os.environ.get("GEMINI_API_KEY") or os.environ.get("GOOGLE_API_KEY")
    if not api_key:
        print(
            "Error: Missing GEMINI_API_KEY (or GOOGLE_API_KEY) environment variable.\n"
            "Configure nanobanana MCP or export GEMINI_API_KEY before using fallback generation.",
            file=sys.stderr,
        )
        return 1

    url = f"https://generativelanguage.googleapis.com/v1beta/models/{args.model}:generateContent?key={api_key}"
    payload = {
        "contents": [{"parts": [{"text": f"[Aspect Ratio: {args.aspect_ratio}] {args.prompt}"}]}],
        "generationConfig": {"responseModalities": ["TEXT", "IMAGE"]},
    }
    req = urllib.request.Request(
        url,
        data=json.dumps(payload).encode("utf-8"),
        headers={"Content-Type": "application/json"},
        method="POST",
    )
    try:
        with urllib.request.urlopen(req, timeout=90) as resp:
            body = json.loads(resp.read().decode("utf-8"))
    except urllib.error.HTTPError as err:
        detail = err.read().decode("utf-8", errors="replace")
        print(f"Gemini API HTTP {err.code}: {detail}", file=sys.stderr)
        return 1
    except Exception as exc:
        print(f"Request failed: {exc}", file=sys.stderr)
        return 1

    for cand in body.get("candidates", []):
        for part in cand.get("content", {}).get("parts", []):
            inline = part.get("inlineData")
            if inline and inline.get("data"):
                out_path = Path(args.output)
                out_path.parent.mkdir(parents=True, exist_ok=True)
                out_path.write_bytes(base64.b64decode(inline["data"]))
                print(f"Saved generated image -> {out_path}")
                return 0

    print(f"No image data returned by model '{args.model}'. Raw response: {json.dumps(body)[:500]}", file=sys.stderr)
    return 1


if __name__ == "__main__":
    sys.exit(main())
