package DemoSelenium;

import com.warrenstrange.googleauth.GoogleAuthenticator;
import java.io.File;
import java.lang.reflect.Method;
import java.nio.file.Files;
import java.time.Duration;
import java.util.List;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.TestInfo;
import org.openqa.selenium.By;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public abstract class BaseTest {

    protected WebDriver driver;
    protected WebDriverWait espera;

    @BeforeEach
    public void abrirChrome() {
        ChromeOptions opciones = new ChromeOptions();

        boolean headless = Boolean.parseBoolean(System.getProperty("headless", "true"));
        if (headless) {
            opciones.addArguments("--headless=new");
        }

        opciones.addArguments("--window-size=1440,900");
        opciones.addArguments("--disable-gpu");

        driver = new ChromeDriver(opciones);
        espera = new WebDriverWait(driver, Duration.ofSeconds(20));
        driver.get(Config.BASE_URL + "/login");
    }

    @AfterEach
    public void cerrarChrome(TestInfo info) {
        if (driver == null) {
            return;
        }

        try {
            String clase = info.getTestClass().map(Class::getSimpleName).orElse("Test");
            String metodo = info.getTestMethod().map(Method::getName).orElse("caso");
            guardarCaptura(clase + "_" + metodo);
        } catch (Exception e) {
            System.out.println("No se pudo guardar la captura: " + e.getMessage());
        }

        driver.quit();
    }

    protected void guardarCaptura(String nombre) throws Exception {
        File carpeta = new File(System.getProperty("user.dir"), "../docs/pruebas/evidencias/selenium");
        carpeta.mkdirs();

        File destino = new File(carpeta, nombre + ".png");
        byte[] imagen = ((TakesScreenshot) driver).getScreenshotAs(OutputType.BYTES);
        Files.write(destino.toPath(), imagen);
    }

    protected void iniciarSesion(String usuario, String clave) {
        espera.until(ExpectedConditions.visibilityOfElementLocated(By.name("username")))
                .sendKeys(usuario);
        driver.findElement(By.name("password")).sendKeys(clave);
        driver.findElement(By.cssSelector("button[type='submit']")).click();

        String secreto = Config.obtenerSecretoOtp(usuario);
        int codigo = new GoogleAuthenticator().getTotpPassword(secreto);
        String codigoTexto = String.format("%06d", codigo);

        espera.until(ExpectedConditions.numberOfElementsToBe(By.cssSelector(".otp-digit"), 6));

        for (int i = 0; i < 6; i++) {
            driver.findElements(By.cssSelector(".otp-digit"))
                    .get(i)
                    .sendKeys(String.valueOf(codigoTexto.charAt(i)));
        }

        driver.findElement(By.cssSelector("button[data-role='verify-btn']")).click();

        espera.until(d -> !d.getCurrentUrl().contains("/login"));
    }

    protected void esperarUrl(String fragmento) {
        espera.until(ExpectedConditions.urlContains(fragmento));
    }

    protected void esperarTexto(String texto) {
        espera.until(ExpectedConditions.visibilityOfElementLocated(
                By.xpath("//*[contains(text(),'" + texto + "')]")));
    }
}
