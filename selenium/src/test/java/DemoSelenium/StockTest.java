package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class StockTest extends BaseTest {

    @Test
    public void actualizar_stock_debeMostrarNuevoValor() {
        iniciarSesion("almacen@cams.com", "cams2026");

        driver.get(Config.BASE_URL + "/productos");
        espera.until(ExpectedConditions.elementToBeClickable(
                By.cssSelector("button[title='Actualizar stock']"))).click();

        WebElement campoStock = espera.until(
                ExpectedConditions.visibilityOfElementLocated(By.cssSelector("dialog[open] input[name='stock']")));
        campoStock.clear();
        campoStock.sendKeys("25");

        driver.findElement(By.cssSelector("dialog[open] button:not([type='button'])")).click();

        espera.until(d -> d.getPageSource().contains("25 unid."));

        Assertions.assertTrue(driver.getPageSource().contains("25 unid."));
    }
}
