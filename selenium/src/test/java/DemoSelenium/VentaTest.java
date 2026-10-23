package DemoSelenium;

import java.util.regex.Matcher;
import java.util.regex.Pattern;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;

public class VentaTest extends BaseTest {

    @Test
    public void registrar_venta_debeGuardarse() {
        iniciarSesion("tienda@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/ventas/nueva");

        WebElement productoSel = espera.until(
                ExpectedConditions.visibilityOfElementLocated(By.name("items[0][producto_id]")));

        Select productos = new Select(productoSel);
        WebElement opcionConMasStock = null;
        int mayorStock = -1;

        for (WebElement opcion : productos.getOptions()) {
            Matcher coincidencia = Pattern.compile("stock:\\s*(\\d+)").matcher(opcion.getText());
            if (coincidencia.find()) {
                int stock = Integer.parseInt(coincidencia.group(1));
                if (stock > mayorStock) {
                    mayorStock = stock;
                    opcionConMasStock = opcion;
                }
            }
        }

        Assertions.assertNotNull(opcionConMasStock, "No hay productos con stock disponible");

        productos.selectByVisibleText(opcionConMasStock.getText());

        WebElement cantidad = driver.findElement(By.name("items[0][cantidad]"));
        cantidad.clear();
        cantidad.sendKeys("1");

        driver.findElement(By.name("cliente_nombre")).sendKeys("Cliente Selenium");
        driver.findElement(By.name("vendedor_nombre")).sendKeys("Vendedor Selenium");

        driver.findElement(By.cssSelector("main form button:not([type='button'])")).click();

        esperarTexto("Venta registrada");

        Assertions.assertTrue(driver.getPageSource().contains("Venta registrada"));
    }
}
