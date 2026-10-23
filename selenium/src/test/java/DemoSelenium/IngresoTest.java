package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;

public class IngresoTest extends BaseTest {

    @Test
    public void registrar_ingreso_debeIncrementarStock() {
        iniciarSesion("almacen@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/inventario/ingreso");

        WebElement productoSel = espera.until(
                ExpectedConditions.visibilityOfElementLocated(By.name("producto_id")));
        new Select(productoSel).selectByIndex(1);

        new Select(driver.findElement(By.name("sucursal_id"))).selectByIndex(0);
        driver.findElement(By.name("cantidad")).sendKeys("5");
        driver.findElement(By.name("motivo")).sendKeys("Ingreso Selenium");

        driver.findElement(By.cssSelector("main form button")).click();

        esperarUrl("/inventario/movimientos");

        Assertions.assertTrue(driver.getPageSource().contains("Ingreso de mercaderia registrado"));
    }
}
