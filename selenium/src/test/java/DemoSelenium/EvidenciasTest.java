package DemoSelenium;

import java.time.Duration;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class EvidenciasTest extends BaseTest {

    private void abrir(String ruta) {
        driver.get(Config.BASE_URL + ruta);
        new WebDriverWait(driver, Duration.ofSeconds(20))
                .until(ExpectedConditions.visibilityOfElementLocated(By.tagName("body")));
    }

    @Test
    public void capturarPantallasDeLosCasos() {
        guardarSeguro("TC-01_login");

        driver.findElement(By.name("username")).sendKeys("admin@cams.com");
        driver.findElement(By.name("password")).sendKeys("clave-mala");
        driver.findElement(By.cssSelector("button[type='submit']")).click();
        espera.until(ExpectedConditions.visibilityOfElementLocated(By.cssSelector(".message.error")));
        guardarSeguro("TC-02_login_error");

        abrir("/recuperar");
        guardarSeguro("TC-04_recuperacion");

        abrir("/login");
        iniciarSesion("admin@cams.com", "cams2026");
        guardarSeguro("TC-01_dashboard");

        abrir("/productos");
        guardarSeguro("TC-13_catalogo");

        abrir("/productos/crear");
        guardarSeguro("TC-13_registrar_producto");

        abrir("/inventario");
        guardarSeguro("TC-19_inventario");

        abrir("/inventario/ingreso");
        guardarSeguro("TC-25_ingreso");

        abrir("/inventario/movimientos");
        guardarSeguro("TC-22_movimientos");

        abrir("/sucursales");
        guardarSeguro("TC-11_sucursales");

        abrir("/sucursales/crear");
        guardarSeguro("TC-10_registrar_sucursal");

        abrir("/ventas/nueva");
        guardarSeguro("TC-28_venta");

        abrir("/ventas");
        guardarSeguro("TC-31_historial");

        abrir("/ventas/nueva");
        driver.findElement(By.name("cliente_nombre")).sendKeys("Cliente Evidencia");
        guardarSeguro("TC-29_formulario_venta");

        Assertions.assertTrue(driver.getPageSource().contains("Venta"));
    }

    private void guardarSeguro(String nombre) {
        try {
            guardarCaptura(nombre);
        } catch (Exception e) {
            System.out.println("No se pudo guardar " + nombre + ": " + e.getMessage());
        }
    }
}
