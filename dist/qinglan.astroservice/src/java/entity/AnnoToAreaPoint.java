/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_to_area_point")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoToAreaPoint.findAll", query = "SELECT a FROM AnnoToAreaPoint a"),
    @NamedQuery(name = "AnnoToAreaPoint.findByAnnoToAreaPointId", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.annoToAreaPointId = :annoToAreaPointId"),
    @NamedQuery(name = "AnnoToAreaPoint.findByRAbl", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.rAbl = :rAbl"),
    @NamedQuery(name = "AnnoToAreaPoint.findByDecbl", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.decbl = :decbl"),
    @NamedQuery(name = "AnnoToAreaPoint.findByTypeBl", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.typeBl = :typeBl"),
    @NamedQuery(name = "AnnoToAreaPoint.findByRAtr", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.rAtr = :rAtr"),
    @NamedQuery(name = "AnnoToAreaPoint.findByDectr", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.dectr = :dectr"),
    @NamedQuery(name = "AnnoToAreaPoint.findByTypeTr", query = "SELECT a FROM AnnoToAreaPoint a WHERE a.typeTr = :typeTr")})
public class AnnoToAreaPoint implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_to_area_point_id")
    private Long annoToAreaPointId;
    @Basic(optional = false)
    @NotNull
    @Column(name = "RA_bl")
    private float rAbl;
    @Basic(optional = false)
    @NotNull
    @Column(name = "Dec_bl")
    private float decbl;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 11)
    @Column(name = "type_bl")
    private String typeBl;
    @Basic(optional = false)
    @NotNull
    @Column(name = "RA_tr")
    private float rAtr;
    @Basic(optional = false)
    @NotNull
    @Column(name = "Dec_tr")
    private float dectr;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 11)
    @Column(name = "type_tr")
    private String typeTr;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @OneToOne(optional = false)
    private Annotation annoSrcId;

    public AnnoToAreaPoint() {
    }

    public AnnoToAreaPoint(Long annoToAreaPointId) {
        this.annoToAreaPointId = annoToAreaPointId;
    }

    public AnnoToAreaPoint(Long annoToAreaPointId, float rAbl, float decbl, String typeBl, float rAtr, float dectr, String typeTr) {
        this.annoToAreaPointId = annoToAreaPointId;
        this.rAbl = rAbl;
        this.decbl = decbl;
        this.typeBl = typeBl;
        this.rAtr = rAtr;
        this.dectr = dectr;
        this.typeTr = typeTr;
    }

    public Long getAnnoToAreaPointId() {
        return annoToAreaPointId;
    }

    public void setAnnoToAreaPointId(Long annoToAreaPointId) {
        this.annoToAreaPointId = annoToAreaPointId;
    }

    public float getRAbl() {
        return rAbl;
    }

    public void setRAbl(float rAbl) {
        this.rAbl = rAbl;
    }

    public float getDecbl() {
        return decbl;
    }

    public void setDecbl(float decbl) {
        this.decbl = decbl;
    }

    public String getTypeBl() {
        return typeBl;
    }

    public void setTypeBl(String typeBl) {
        this.typeBl = typeBl;
    }

    public float getRAtr() {
        return rAtr;
    }

    public void setRAtr(float rAtr) {
        this.rAtr = rAtr;
    }

    public float getDectr() {
        return dectr;
    }

    public void setDectr(float dectr) {
        this.dectr = dectr;
    }

    public String getTypeTr() {
        return typeTr;
    }

    public void setTypeTr(String typeTr) {
        this.typeTr = typeTr;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoToAreaPointId != null ? annoToAreaPointId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoToAreaPoint)) {
            return false;
        }
        AnnoToAreaPoint other = (AnnoToAreaPoint) object;
        if ((this.annoToAreaPointId == null && other.annoToAreaPointId != null) || (this.annoToAreaPointId != null && !this.annoToAreaPointId.equals(other.annoToAreaPointId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoToAreaPoint[ annoToAreaPointId=" + annoToAreaPointId + " ]";
    }
    
}
