package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class MovimientoTest extends BaseTest {

    @Test
    public void consultar_movimientos_debeMostrarListado() {
        iniciarSesion("almacen@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/inventario/movimientos");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.tagName("table")));

        Assertions.assertTrue(driver.getPageSource().contains("Movimientos de inventario"));
    }
}
