package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class InventarioTest extends BaseTest {

    @Test
    public void consultar_inventario_debeMostrarStock() {
        iniciarSesion("almacen@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/inventario");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.tagName("table")));

        String pagina = driver.getPageSource();
        Assertions.assertTrue(pagina.contains("Inventario"));
        Assertions.assertTrue(pagina.contains("unid."));
    }
}
