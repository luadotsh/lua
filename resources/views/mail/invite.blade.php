<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <style>
    td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family:  "Inter", "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
  </style>
  <![endif]-->
  <title>{{ $title }}</title>
  <style>
    .hover-important-text-decoration-underline:hover {
      text-decoration: underline !important
    }
    @media (max-width: 600px) {
      .sm-my-8 {
        margin-top: 32px !important;
        margin-bottom: 32px !important
      }
      .sm-px-4 {
        padding-left: 16px !important;
        padding-right: 16px !important
      }
      .sm-px-6 {
        padding-left: 24px !important;
        padding-right: 24px !important
      }
      .sm-leading-8 {
        line-height: 32px !important
      }
    }
  </style>
</head>
<body style="margin: 0; width: 100%; padding: 0; -webkit-font-smoothing: antialiased; word-break: break-word">
  <div role="article" aria-roledescription="email" aria-label="{{ $title || '' }}">
    <div class="sm-px-4" style="background-color: #fafafa; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif">
      <table align="center" cellpadding="0" cellspacing="0" role="none">
        <tr>
          <td style="width: 552px; max-width: 100%">
            <div class="sm-my-8" style="margin-top: 48px; margin-bottom: 48px; text-align: center">
              <a href="https://lua.sh" target="_blank">
                <img src="{{ asset('images/lua/full-black.svg') }}" width="160" alt="Lua.sh" style="max-width: 100%; vertical-align: middle">
              </a>
            </div>
            <table style="width: 100%;" cellpadding="0" cellspacing="0" role="none">
              <tr>
                <td class="sm-px-6" style="border-radius: 4px; background-color: #fffffe; padding: 48px; font-size: 16px; color: #27272a; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)">
                  <h1 class="sm-leading-8" style="margin: 0 0 24px; font-size: 24px; font-weight: 600; color: #000001">
                    Hello 👋
                  </h1>
                  <p style="margin: 0; line-height: 24px">
                    You have been invited to join the {{ $workspace->name }} team.
                    <br>
                    <br>
                    To accept the invitation and be part of this team, please click on the button below.
                  </p>
                  <div role="separator" style="line-height: 24px">&zwj;</div>
                  <div style="display: flex; align-items: center; justify-content: center">
                    <div>
                      <a href="{{ $url }}" target="_blank" style="display: inline-block; border-radius: 6px; background-color: #18181b; padding: 12px 32px; font-size: 16px; font-weight: 600; line-height: 1; color: #fffffe; text-decoration: none">
                        <!--[if mso]>
      <i style="mso-font-width: 150%; mso-text-raise: 30px" hidden>&emsp;</i>
    <![endif]-->
                        <span style="mso-text-raise: 16px">
                    Accept invite &rarr;
                  </span>
                        <!--[if mso]>
      <i hidden style="mso-font-width: 150%;">&emsp;&#8203;</i>
    <![endif]-->
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td style="padding: 24px; text-align: center; font-size: 12px; color: #52525b">
                  <p style="margin: 0 0 8px">
                    © 2024 Lua.sh LLC.
                  </p>
                  <p style="margin: 0;">
                    651 N Broad St, Suite 201, Middletown, DE 19709, USA
                  </p>
                  <p style="cursor: default">
                    <a href="https://lua.sh/github" target="_blank" class="hover-important-text-decoration-underline" style="color: #52525b; text-decoration: none;">Github</a>
                    &bull;
                    <a href="https://lua.sh/x" target="_blank" class="hover-important-text-decoration-underline" style="color: #52525b; text-decoration: none;">Twitter</a>
                  </p>
                  @if(isset($unsubscribe_url))
                  <p>
                    <a target="_blank" class="hover-important-text-decoration-underline" style="color: #52525b; text-decoration: none;">
                      Unsubscribe
                    </a>
                  </p>
                  @endif
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>