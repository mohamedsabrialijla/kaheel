from pathlib import Path
from PIL import Image, ImageDraw

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / "public"


def rounded_mask(size: int, radius: int) -> Image.Image:
    mask = Image.new("L", (size, size), 0)
    draw = ImageDraw.Draw(mask)
    draw.rounded_rectangle((0, 0, size - 1, size - 1), radius=radius, fill=255)
    return mask


def draw_mark(size: int) -> Image.Image:
    img = Image.new("RGBA", (size, size), (0, 85, 115, 255))
    draw = ImageDraw.Draw(img)
    s = size / 64
    path = [
        (19 * s, 14 * s),
        (29 * s, 14 * s),
        (29 * s, 27.6 * s),
        (42.2 * s, 14 * s),
        (54 * s, 14 * s),
        (36.4 * s, 32.2 * s),
        (54 * s, 50 * s),
        (42.1 * s, 50 * s),
        (29 * s, 36.4 * s),
        (29 * s, 50 * s),
        (19 * s, 50 * s),
    ]
    draw.polygon(path, fill=(255, 255, 255, 255))
    canvas = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    canvas.paste(img, mask=rounded_mask(size, int(14 * s)))
    return canvas


def save_png(image: Image.Image, path: Path, size: int) -> None:
    image.resize((size, size), Image.Resampling.LANCZOS).save(path, "PNG")
    print("wrote", path)


master = draw_mark(1024)
save_png(master, OUT / "kaheel" / "favicon.png", 32)
save_png(master, OUT / "kaheel" / "apple-touch-icon.png", 180)
save_png(master, OUT / "assets" / "media" / "logos" / "fav.png", 192)

ico_sizes = [16, 32, 48]
ico_images = [master.resize((n, n), Image.Resampling.LANCZOS) for n in ico_sizes]
ico_path = OUT / "favicon.ico"
ico_images[0].save(ico_path, format="ICO", sizes=[(n, n) for n in ico_sizes], append_images=ico_images[1:])
print("wrote", ico_path)
