<?php

function get_attachment_mime_type($post_id) {
  $type = get_post_mime_type($post_id);
  switch ($type) {

    // PDFs
    case 'application/pdf':
      return 'PDF'; break;

    // Images
    case 'image/jpeg':
    case 'image/gif':
    case 'image/png':
    case 'image/bmp':
    case 'image/tiff':
    case 'image/x-icon':
      return 'IMAGE'; break;

    // Videos
    case 'video/x-ms-asf':
    case 'video/x-ms-wmv':
    case 'video/x-ms-wmx':
    case 'video/x-ms-wm':
    case 'video/avi':
    case 'video/divx':
    case 'video/x-flv':
    case 'video/quicktime':
    case 'video/mpeg':
    case 'video/mp4':
    case 'video/ogg':
    case 'video/webm':
    case 'video/x-matroska':
      return 'VIDEO'; break;

    // CSVs
    case 'text/csv':
      return 'CSV'; break;

    // Text formats
    case 'text/plain':
    case 'text/tab-separated-values':
    case 'text/calendar':
    case 'text/richtext':
    case 'text/css':
    case 'text/html':
    case 'text/xml':
    case 'application/rtf':
    case 'application/javascript':
    case 'application/java':
      return 'TEXT'; break;

    // Audio formats
    case 'audio/mpeg':
    case 'audio/x-realaudio':
    case 'audio/wav':
    case 'audio/ogg':
    case 'audio/midi':
    case 'audio/x-ms-wma':
    case 'audio/x-ms-wax':
    case 'audio/x-matroska':
      return 'AUDIO'; break;

    // Misc application formats
    case 'application/x-shockwave-flash':
      return 'SWF'; break;

    case 'application/x-tar':
    case 'application/x-7z-compressed':
    case 'application/x-msdownload':
    case 'application/rar':
    case 'application/zip':
    case 'application/x-gzip':
      return 'ARCHIVE'; break;

    // MS Office formats
    case 'application/msword':
      return 'WORD'; break;
    case 'application/vnd.ms-powerpoint':
      return 'PPT'; break;
    case 'application/vnd.ms-excel':
    return 'XLS'; break;
    case 'application/vnd.ms-write':
    case 'application/vnd.ms-access':
    case 'application/vnd.ms-project':
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    case 'application/vnd.ms-word.document.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
    case 'application/vnd.ms-word.template.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
    case 'application/vnd.ms-excel.sheet.macroEnabled.12':
    case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
    case 'application/vnd.ms-excel.template.macroEnabled.12':
    case 'application/vnd.ms-excel.addin.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
    case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
    case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.presentationml.template':
    case 'application/vnd.ms-powerpoint.template.macroEnabled.12':
    case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
    case 'application/vnd.openxmlformats-officedocument.presentationml.slide':
    case 'application/vnd.ms-powerpoint.slide.macroEnabled.12':
    case 'application/onenote':
      return ''; break;

    // OpenOffice formats
    case 'application/vnd.oasis.opendocument.text':
    case 'application/vnd.oasis.opendocument.presentation':
    case 'application/vnd.oasis.opendocument.spreadsheet':
    case 'application/vnd.oasis.opendocument.graphics':
    case 'application/vnd.oasis.opendocument.chart':
    case 'application/vnd.oasis.opendocument.database':
    case 'application/vnd.oasis.opendocument.formula':
      return ''; break;

      // WordPerfect formats
    case 'application/wordperfect':
      return ''; break;

      // iWork formats
    case 'application/vnd.apple.keynote':
      return 'KEYNOTE'; break;
    case 'application/vnd.apple.numbers':
      return 'NUMBERS'; break;
    case 'application/vnd.apple.pages':
      return 'PAGES'; break;

    default:
      return '';
  }
}
