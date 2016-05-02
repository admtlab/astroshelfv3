/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.Access;
import javax.persistence.AccessType;
import javax.persistence.Embeddable;

/**
 *
 * @author panickos
 */
@Access(AccessType.PROPERTY)
@Embeddable
public class SkyPoint implements Serializable {
    
    private Double ra;
    private Double dec;

    public SkyPoint() {
    }

    public SkyPoint(Double ra, Double dec) {
        this.ra = ra;
        this.dec = dec;
    }

    public Double getRa() {
        return ra;
    }

    public void setRa(Double ra) {
        this.ra = ra;
    }

    public Double getDec() {
        return dec;
    }

    public void setDec(Double dec) {
        this.dec = dec;
    }
    
    
}
