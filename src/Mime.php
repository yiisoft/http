<?php

declare(strict_types=1);

namespace Yiisoft\Http;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
 * @link https://github.com/mdn/content/blob/main/files/en-us/web/http/basics_of_http/mime_types/common_types/index.md
 */
final class Mime
{
    /**
     * AAC audio
     * File extension: .aac
     */
    public const AUDIO_AAC = 'audio/aac';
    /**
     * AbiWord document
     * @link https://en.wikipedia.org/wiki/AbiWord
     * File extension: .abw
     */
    public const APPLICATION_X_ABIWORD = 'application/x-abiword';
    /**
     * Archive document (multiple files embedded)
     * File extension: .arc
     */
    public const APPLICATION_X_FREEARC = 'application/x-freearc';
    /**
     * AVIF image
     * File extension: .avif
     */
    public const IMAGE_AVIF = 'image/avif';
    /**
     * AVI: Audio Video Interleave
     * File extension: .avi
     */
    public const VIDEO_X_MSVIDEO = 'video/x-msvideo';
    /**
     * Amazon Kindle eBook format
     * File extension: .azw
     */
    public const APPLICATION_VND_AMAZON_EBOOK = 'application/vnd.amazon.ebook';
    /**
     * Any kind of binary data
     * File extension: .bin
     */
    public const APPLICATION_OCTET_STREAM = 'application/octet-stream';
    /**
     * Windows OS/2 Bitmap Graphics
     * File extension: .bmp
     */
    public const IMAGE_BMP = 'image/bmp';
    /**
     * BZip archive
     * File extension: .bz
     */
    public const APPLICATION_X_BZIP = 'application/x-bzip';
    /**
     * BZip2 archive
     * File extension: .bz2
     */
    public const APPLICATION_X_BZIP2 = 'application/x-bzip2';
    /**
     * CD audio
     * File extension: .cda
     */
    public const APPLICATION_X_CDF = 'application/x-cdf';
    /**
     * C-Shell script
     * File extension: .csh
     */
    public const APPLICATION_X_CSH = 'application/x-csh';
    /**
     * Cascading Style Sheets (CSS)
     * File extension: .css
     */
    public const TEXT_CSS = 'text/css';
    /**
     * Comma-separated values (CSV)
     * File extension: .csv
     */
    public const TEXT_CSV = 'text/csv';
    /**
     * Microsoft Word
     * File extension: .doc
     */
    public const APPLICATION_MSWORD = 'application/msword';
    /**
     * Microsoft Word (OpenXML)
     * File extension: .docx
     */
    public const APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_WORDPROCESSINGML_DOCUMENT = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    /**
     * MS Embedded OpenType fonts
     * File extension: .eot
     */
    public const APPLICATION_VND_MS_FONTOBJECT = 'application/vnd.ms-fontobject';
    /**
     * Electronic publication (EPUB)
     * File extension: .epub
     */
    public const APPLICATION_EPUB_ZIP = 'application/epub+zip';
    /**
     * GZip Compressed Archive
     * File extension: .gz
     */
    public const APPLICATION_GZIP = 'application/gzip';
    /**
     * Graphics Interchange Format (GIF)
     * File extension: .gif
     */
    public const IMAGE_GIF = 'image/gif';
    /**
     * HyperText Markup Language (HTML)
     * File extension: .htm, .html
     */
    public const TEXT_HTML = 'text/html';
    /**
     * Icon format
     * File extension: .ico
     */
    public const IMAGE_VND_MICROSOFT_ICON = 'image/vnd.microsoft.icon';
    /**
     * iCalendar format
     * File extension: .ics
     */
    public const TEXT_CALENDAR = 'text/calendar';
    /**
     * Java Archive (JAR)
     * File extension: .jar
     */
    public const APPLICATION_JAVA_ARCHIVE = 'application/java-archive';
    /**
     * JPEG images
     * File extension: .jpeg, .jpg
     */
    public const IMAGE_JPEG = 'image/jpeg';
    /**
     * JavaScript
     * @link https://html.spec.whatwg.org/multipage/#scriptingLanguages
     * @link https://www.rfc-editor.org/rfc/rfc9239
     * File extension: .js, .mjs, .cjs
     */
    public const TEXT_JAVASCRIPT = 'text/javascript';
    /**
     * JSON format
     * File extension: .json
     */
    public const APPLICATION_JSON = 'application/json';
    /**
     * JSON-LD format
     * File extension: .jsonld
     */
    public const APPLICATION_LD_JSON = 'application/ld+json';
    /**
     * Musical Instrument Digital Interface (MIDI)
     * File extension: .mid, .midi
     */
    public const AUDIO_MIDI = 'audio/midi';
    /**
     * Musical Instrument Digital Interface (MIDI)
     * File extension: .mid, .midi
     */
    public const AUDIO_X_MIDI = 'audio/x-midi';
    /**
     * MP3 audio
     * File extension: .mp3
     */
    public const AUDIO_MPEG = 'audio/mpeg';
    /**
     * MP4 video
     * File extension: .mp4
     */
    public const VIDEO_MP4 = 'video/mp4';
    /**
     * MPEG Video
     * File extension: .mpeg
     */
    public const VIDEO_MPEG = 'video/mpeg';
    /**
     * Apple Installer Package
     * File extension: .mpkg
     */
    public const APPLICATION_VND_APPLE_INSTALLER_XML = 'application/vnd.apple.installer+xml';
    /**
     * OpenDocument presentation document
     * File extension: .odp
     */
    public const APPLICATION_VND_OASIS_OPENDOCUMENT_PRESENTATION = 'application/vnd.oasis.opendocument.presentation';
    /**
     * OpenDocument spreadsheet document
     * File extension: .ods
     */
    public const APPLICATION_VND_OASIS_OPENDOCUMENT_SPREADSHEET = 'application/vnd.oasis.opendocument.spreadsheet';
    /**
     * OpenDocument text document
     * File extension: .odt
     */
    public const APPLICATION_VND_OASIS_OPENDOCUMENT_TEXT = 'application/vnd.oasis.opendocument.text';
    /**
     * OGG audio
     * File extension: .oga
     */
    public const AUDIO_OGG = 'audio/ogg';
    /**
     * OGG video
     * File extension: .ogv
     */
    public const VIDEO_OGG = 'video/ogg';
    /**
     * OGG
     * File extension: .ogx
     */
    public const APPLICATION_OGG = 'application/ogg';
    /**
     * Opus audio
     * File extension: .opus
     */
    public const AUDIO_OPUS = 'audio/opus';
    /**
     * OpenType font
     * File extension: .otf
     */
    public const FONT_OTF = 'font/otf';
    /**
     * Portable Network Graphics
     * File extension: .png
     */
    public const IMAGE_PNG = 'image/png';
    /**
     * Adobe Portable Document Format (PDF)
     * @link https://www.adobe.com/acrobat/about-adobe-pdf.html
     * File extension: .pdf
     */
    public const APPLICATION_PDF = 'application/pdf';
    /**
     * Hypertext Preprocessor (**Personal Home Page**)
     * File extension: .php
     */
    public const APPLICATION_X_HTTPD_PHP = 'application/x-httpd-php';
    /**
     * Microsoft PowerPoint
     * File extension: .ppt
     */
    public const APPLICATION_VND_MS_POWERPOINT = 'application/vnd.ms-powerpoint';
    /**
     * Microsoft PowerPoint (OpenXML)
     * File extension: .pptx
     */
    public const APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_PRESENTATIONML_PRESENTATION = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    /**
     * RAR archive
     * File extension: .rar
     */
    public const APPLICATION_VND_RAR = 'application/vnd.rar';
    /**
     * Rich Text Format (RTF)
     * File extension: .rtf
     */
    public const APPLICATION_RTF = 'application/rtf';
    /**
     * Bourne shell script
     * File extension: .sh
     */
    public const APPLICATION_X_SH = 'application/x-sh';
    /**
     * Scalable Vector Graphics (SVG)
     * File extension: .svg
     */
    public const IMAGE_SVG_XML = 'image/svg+xml';
    /**
     * Tape Archive (TAR)
     * File extension: .tar
     */
    public const APPLICATION_X_TAR = 'application/x-tar';
    /**
     * Tagged Image File Format (TIFF)
     * File extension: .tif, .tiff
     */
    public const IMAGE_TIFF = 'image/tiff';
    /**
     * MPEG transport stream
     * File extension: .ts
     */
    public const VIDEO_MP2T = 'video/mp2t';
    /**
     * TrueType Font
     * File extension: .ttf
     */
    public const FONT_TTF = 'font/ttf';
    /**
     * Text, (generally ASCII or ISO 8859-_n_)
     * File extension: .txt
     */
    public const TEXT_PLAIN = 'text/plain';
    /**
     * Microsoft Visio
     * File extension: .vsd
     */
    public const APPLICATION_VND_VISIO = 'application/vnd.visio';
    /**
     * Waveform Audio Format
     * File extension: .wav
     */
    public const AUDIO_WAV = 'audio/wav';
    /**
     * WEBM audio
     * File extension: .weba
     */
    public const AUDIO_WEBM = 'audio/webm';
    /**
     * WEBM video
     * File extension: .webm
     */
    public const VIDEO_WEBM = 'video/webm';
    /**
     * WEBP image
     * File extension: .webp
     */
    public const IMAGE_WEBP = 'image/webp';
    /**
     * Web Open Font Format (WOFF)
     * File extension: .woff
     */
    public const FONT_WOFF = 'font/woff';
    /**
     * Web Open Font Format (WOFF)
     * File extension: .woff2
     */
    public const FONT_WOFF2 = 'font/woff2';
    /**
     * XHTML
     * File extension: .xhtml
     */
    public const APPLICATION_XHTML_XML = 'application/xhtml+xml';
    /**
     * Microsoft Excel
     * File extension: .xls
     */
    public const APPLICATION_VND_MS_EXCEL = 'application/vnd.ms-excel';
    /**
     * Microsoft Excel (OpenXML)
     * File extension: .xlsx
     */
    public const APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_SPREADSHEETML_SHEET = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    /**
     * XML
     * File extension: .xml
     */
    public const APPLICATION_XML = 'application/xml';
    /**
     * XUL
     * File extension: .xul
     */
    public const APPLICATION_VND_MOZILLA_XUL_XML = 'application/vnd.mozilla.xul+xml';
    /**
     * ZIP archive
     * File extension: .zip
     */
    public const APPLICATION_ZIP = 'application/zip';
    /**
     * 3GPP2 audio/video container
     * @link https://en.wikipedia.org/wiki/3GP_and_3G2
     * File extension: .3gp
     */
    public const VIDEO_3GPP = 'video/3gpp';
    /**
     * 3GPP2 audio/video container if it doesn't contain video
     * @link https://en.wikipedia.org/wiki/3GP_and_3G2
     * File extension: .3g2
     */
    public const AUDIO_3GPP = 'audio/3gpp';
    /**
     * 3GPP2 audio/video container
     * @link https://en.wikipedia.org/wiki/3GP_and_3G2
     * File extension: .3g2
     */
    public const VIDEO_3GPP2 = 'video/3gpp2';
    /**
     * 3GPP2 audio/video container if it doesn't contain video
     * @link https://en.wikipedia.org/wiki/3GP_and_3G2
     * File extension: .3g2
     */
    public const AUDIO_3GPP2 = 'audio/3gpp2';
    /**
     * 7-zip archive
     * @link https://en.wikipedia.org/wiki/7-Zip
     * File extension: .7z
     */
    public const APPLICATION_X_7Z_COMPRESSED = 'application/x-7z-compressed';
}
