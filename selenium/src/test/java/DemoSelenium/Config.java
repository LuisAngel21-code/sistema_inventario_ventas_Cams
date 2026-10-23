package DemoSelenium;

public class Config {

    public static final String BASE_URL = "http://localhost:8000";

    // Secreto TOTP fijo de los usuarios de prueba (Google Authenticator)
    public static final String SECRETO_OTP = "JBSWY3DPEHPK3PXP";

    public static String obtenerSecretoOtp(String usuario) {
        return SECRETO_OTP;
    }
}
