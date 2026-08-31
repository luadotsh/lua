/**
 * `qrcode` ships no types and `@types/qrcode` would be a new dependency, so
 * the two calls this project makes are declared here instead. Widen it if a
 * third call is ever needed rather than reaching for `any` at the call site.
 */
declare module 'qrcode' {
    type Options = {
        errorCorrectionLevel?: 'L' | 'M' | 'Q' | 'H';
        margin?: number;
        width?: number;
        type?: 'svg' | 'utf8' | 'terminal';
    };

    const QRCode: {
        toDataURL(text: string, options?: Options): Promise<string>;
        toString(text: string, options?: Options): Promise<string>;
    };

    export default QRCode;
}
