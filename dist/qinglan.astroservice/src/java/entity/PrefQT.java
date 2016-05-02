/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "prefQT")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "PrefQT.findAll", query = "SELECT p FROM PrefQT p"),
    @NamedQuery(name = "PrefQT.findByPrefqtId", query = "SELECT p FROM PrefQT p WHERE p.prefqtId = :prefqtId"),
    @NamedQuery(name = "PrefQT.findByPredicate", query = "SELECT p FROM PrefQT p WHERE p.predicate = :predicate"),
    @NamedQuery(name = "PrefQT.findByIntensity", query = "SELECT p FROM PrefQT p WHERE p.intensity = :intensity"),
    @NamedQuery(name = "PrefQT.findByPrefName", query = "SELECT p FROM PrefQT p WHERE p.prefName = :prefName")})
public class PrefQT implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "prefqt_id")
    private Long prefqtId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 100)
    @Column(name = "predicate")
    private String predicate;
    @Basic(optional = false)
    @NotNull
    @Column(name = "intensity")
    private double intensity;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 20)
    @Column(name = "prefName")
    private String prefName;
    @JoinTable(name = "prefAnn_To_PrefQT", joinColumns = {
        @JoinColumn(name = "prefqt_id", referencedColumnName = "prefqt_id")}, inverseJoinColumns = {
        @JoinColumn(name = "pref_on_ann_id", referencedColumnName = "pref_id")})
    @ManyToMany(cascade = CascadeType.PERSIST, mappedBy = "prefQTCollection")
    private Collection<PrefAnnotation> prefAnnotationCollection;
    
    @JoinColumn(name = "user_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;

    public PrefQT() {
    }

    public PrefQT(Long prefqtId) {
        this.prefqtId = prefqtId;
    }

    public PrefQT(Long prefqtId, String predicate, double intensity, String prefName) {
        this.prefqtId = prefqtId;
        this.predicate = predicate;
        this.intensity = intensity;
        this.prefName = prefName;
    }

    public Long getPrefqtId() {
        return prefqtId;
    }

    public void setPrefqtId(Long prefqtId) {
        this.prefqtId = prefqtId;
    }

    public String getPredicate() {
        return predicate;
    }

    public void setPredicate(String predicate) {
        this.predicate = predicate;
    }

    public double getIntensity() {
        return intensity;
    }

    public void setIntensity(double intensity) {
        this.intensity = intensity;
    }

    public String getPrefName() {
        return prefName;
    }

    public void setPrefName(String prefName) {
        this.prefName = prefName;
    }

    //@XmlTransient
    public Collection<PrefAnnotation> getPrefAnnotationCollection() {
        return prefAnnotationCollection;
    }

    public void setPrefAnnotationCollection(Collection<PrefAnnotation> prefAnnotationCollection) {
        this.prefAnnotationCollection = prefAnnotationCollection;
    }

    public User getUserId() {
        return userId;
    }

    public void setUserId(User userId) {
        this.userId = userId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (prefqtId != null ? prefqtId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof PrefQT)) {
            return false;
        }
        PrefQT other = (PrefQT) object;
        if ((this.prefqtId == null && other.prefqtId != null) || (this.prefqtId != null && !this.prefqtId.equals(other.prefqtId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.PrefQT[ prefqtId=" + prefqtId + " ]";
    }
    
}
