package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class HistorialTest extends BaseTest {

    @Test
    public void consultar_historial_debeMostrarVentas() {
        iniciarSesion("tienda@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/ventas");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.tagName("table")));

        Assertions.assertTrue(driver.getPageSource().contains("Historial de ventas"));
    }
}
