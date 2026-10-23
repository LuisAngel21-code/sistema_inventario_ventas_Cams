package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class CodigoTest extends BaseTest {

    @Test
    public void verificar_codigoValido_debePermitirAcceso() {
        iniciarSesion("admin@cams.com", "cams2026");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.tagName("body")));

        Assertions.assertTrue(driver.getPageSource().contains("Dashboard"));
    }
}
