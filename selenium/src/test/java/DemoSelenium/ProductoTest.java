package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;

public class ProductoTest extends BaseTest {

    @Test
    public void registrar_producto_debeGuardarseYVerseEnCatalogo() {
        iniciarSesion("almacen@cams.com", "cams2026");

        String codigo = "SEL-" + System.currentTimeMillis();
        String nombre = "Cama Selenium";

        driver.get(Config.BASE_URL + "/productos/crear");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.name("codigo"))).sendKeys(codigo);
        new Select(driver.findElement(By.name("categoria"))).selectByVisibleText("Camas");
        driver.findElement(By.name("nombre")).sendKeys(nombre);
        driver.findElement(By.name("costo")).sendKeys("1000");
        driver.findElement(By.name("precio_base")).sendKeys("1500");
        driver.findElement(By.name("stock")).sendKeys("10");
        driver.findElement(By.name("stock_minimo")).sendKeys("2");
        driver.findElement(By.cssSelector("main form button")).click();

        esperarTexto("Producto registrado correctamente");

        Assertions.assertTrue(driver.getPageSource().contains("Producto registrado correctamente"));
    }
}
